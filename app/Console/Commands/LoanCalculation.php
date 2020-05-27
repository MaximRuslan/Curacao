<?php

namespace App\Console\Commands;

use App\Library\FirebaseHelper;
use App\Library\Helper;
use App\Mail\DefaultAfterLoanReminderMail;
use App\Mail\DefaultBeforeLoanReminderMail;
use App\Models\Country;
use App\Models\EmailHistory;
use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanStatusHistory;
use App\Models\LoanTransaction;
use App\Models\LoanType;
use App\Models\Template;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoanCalculation extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:calculate {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loan calculation process which will run every day.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('id') == 'before') {
            //before mail
            $loan_applications = LoanApplication::where('loan_status', '=', 4)->get();
            Log::info($loan_applications->pluck('id'));

            foreach ($loan_applications as $key => $loan) {
                $user = User::find($loan->client_id);
                $country = Country::find($user->country);
                $last_histroy_week = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                    ->whereNull('payment_amount')
                    ->orderBy('id', 'desc')
                    ->first();
                if (Helper::database_date_to_current_timezone($last_histroy_week->created_at, $country->timezone) <= Helper::database_date_to_current_timezone(date('Y-m-d H:i:s',
                        strtotime('-6 days')), $country->timezone)) {
                    $email_history = EmailHistory::where('email_class', '=', 'App\Mail\DefaultBeforeLoanReminderMail')
                        ->where('model_id', '=', $loan->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    if ($email_history == null) {
                        $outstanding_balance = 0;
                        $last_histroy = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                            ->orderBy('id', 'desc')
                            ->first();
                        $loan_type = LoanType::find($loan->loan_type);
                        $loan_type->period = $loan_type->number_of_days;
                        if ($last_histroy != null && ($last_histroy->week_iterations + 1) == $loan_type->period) {
                            $outstanding_balance = $last_histroy->total;
                            try {
                                Mail::to($user->email)->send(new DefaultBeforeLoanReminderMail($user, $loan, $outstanding_balance));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }

                            $data = [
                                'app_name'            => config('app.name'),
                                'client_name'         => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
                                'loan_id'             => $loan->id,
                                'outstanding_balance' => $outstanding_balance,
                            ];

                            $template = Template::findFromKey('loan_default_before_reminder_message', 2, $user->lang, $data);

                            $data = [];

                            FirebaseHelper::firebaseNotification($user->id, $template->subject, $template->content, 'default_before_reminder', $data);
                        }
                    }
                }
            }
        }

        if ($this->argument('id') != 'before' && $this->argument('id') != 'after') {
            //process
            $loans = LoanApplication::whereIn('loan_status', ['4', '5', '6'])
                ->whereDate('start_date', '<=', date('Y-m-d'));

            if ($this->argument('id') != 'all') {
                $loans->where('id', '=', $this->argument('id'));
            }

            $loans = $loans->get();

            foreach ($loans as $key => $loan) {
                $loan_type = LoanType::find($loan->loan_type);

                $loan_type->period = $loan_type->number_of_days;

                $last_histroy = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                    ->orderBy('id', 'desc')
                    ->first();

                $today_transaction = LoanTransaction::where('payment_date', '<=', date('Y-m-d'))
                    ->where('loan_id', '=', $loan->id)
                    ->where('used', '=', 0)
                    ->get();

                $today_transaction_ids = $today_transaction->pluck('id');
                $payment_entry = false;
                if ($today_transaction->count() > 0) {
                    //payments
                    $payments = $today_transaction->whereIn('transaction_type', ['1', '3']);

                    $payment = $payment_amount = $payments->sum('amount') - $payments->sum('cash_back_amount');

                    if ($payment > 0) {
                        $week_iteration = $last_histroy['week_iterations'];
                        $amount = $last_histroy['principal'];
                        $origination_fee = 0;

                        $debt_collection_fee = $last_histroy['debt_collection_value'];
                        $debt_collection_tax = $last_histroy['debt_collection_tax'];
                        $debt = $last_histroy['debt'];
                        $debt_tax = $last_histroy['debt_tax'];
                        $renewal = $last_histroy['renewal'];
                        $tax_for_renewal = $last_histroy['tax_for_renewal'];
                        $interest = $last_histroy['interest'];
                        $tax_for_interest = $last_histroy['tax_for_interest'];
                        $extra = 0;

                        $debt_posted = 0;
                        $debt_tax_posted = 0;
                        $debt_collection_value_posted = 0;
                        $debt_collection_tax_posted = 0;
                        $renewal_posted = 0;
                        $renewal_tax_posted = 0;
                        $interest_posted = 0;
                        $interest_tax_posted = 0;
                        $principal_posted = 0;

                        if ($debt + $debt_tax > $payment) {
                            $old_debt = 0;
                            $old_debt_tax = 0;
                            $remaing_total_debt = $debt_tax + $debt - $payment;
                            if ($loan_type->debt_tax_type == 1) {
                                $portion_tax = 1 + ($loan_type->debt_tax_amount / 100);
                            } else {
                                $tax_percentage = ($debt_tax * 100 / $debt);
                                $portion_tax = 1 + ($tax_percentage / 100);
                            }
                            $debt = round($remaing_total_debt / $portion_tax, 2);

                            $debt_tax = $remaing_total_debt - $debt;

                            $debt_posted = $old_debt - $debt;
                            $debt_tax_posted = $old_debt_tax - $debt_tax;

                            $payment = 0;
                        } else {
                            $payment = $payment - $debt_tax - $debt;
                            $debt_posted = $debt;
                            $debt_tax_posted = $debt_tax;
                            $debt = $debt_tax = 0;
                            if ($debt_collection_fee + $debt_collection_tax > $payment) {
                                $old_debt_collection = $debt_collection_fee;
                                $old_debt_collection_tax = $debt_collection_tax;
                                $remaing_total_debt_collection = $debt_collection_tax + $debt_collection_fee - $payment;
                                $portion_debt_collection_tax = 1 + ($loan->tax_percentage / 100);
                                $debt_collection_fee = round($remaing_total_debt_collection / $portion_debt_collection_tax, 2);
                                $debt_collection_tax = $remaing_total_debt_collection - $debt_collection_fee;
                                $debt_collection_value_posted = $old_debt_collection - $debt_collection_fee;
                                $debt_collection_tax_posted = $old_debt_collection_tax - $debt_collection_tax;
                                $payment = 0;
                            } else {
                                $payment = $payment - $debt_collection_fee - $debt_collection_tax;
                                $debt_collection_value_posted = $debt_collection_fee;
                                $debt_collection_tax_posted = $debt_collection_tax;
                                $debt_collection_tax = $debt_collection_fee = 0;

                                if ($tax_for_renewal + $renewal > $payment) {
                                    $old_renewal = $renewal;
                                    $old_renewal_tax = $tax_for_renewal;
                                    $remaing_total_renewal = $tax_for_renewal + $renewal - $payment;
                                    $portion_tax = 1 + ($loan->tax_percentage / 100);
                                    $renewal = round($remaing_total_renewal / $portion_tax, 2);
                                    $tax_for_renewal = $remaing_total_renewal - $renewal;
                                    $renewal_posted = $old_renewal - $renewal;
                                    $renewal_tax_posted = $old_renewal_tax - $tax_for_renewal;
                                    $payment = 0;
                                } else {
                                    $payment = $payment - $renewal - $tax_for_renewal;
                                    $renewal_posted = $renewal;
                                    $renewal_tax_posted = $tax_for_renewal;
                                    $renewal = $tax_for_renewal = 0;

                                    if ($interest + $tax_for_interest > $payment) {
                                        $old_interest = $interest;
                                        $old_interest_tax = $tax_for_interest;
                                        $remaing_total_interest = $interest + $tax_for_interest - $payment;
                                        $portion_tax = 1 + ($loan->tax_percentage / 100);
                                        $interest = round($remaing_total_interest / $portion_tax, 2);
                                        $tax_for_interest = $remaing_total_interest - $interest;
                                        $interest_posted = $old_interest - $interest;
                                        $interest_tax_posted = $old_interest_tax - $tax_for_interest;
                                        $payment = 0;
                                    } else {
                                        $payment = $payment - $tax_for_interest - $interest;
                                        $interest_posted = $interest;
                                        $interest_tax_posted = $tax_for_interest;
                                        $interest = $tax_for_interest = 0;

                                        if ($last_histroy['principal'] > $payment) {
                                            $amount = $last_histroy['principal'] - $payment;
                                            $principal_posted = $payment;
                                        } else {
                                            if ($payment > $last_histroy['principal']) {
                                                $amount = $last_histroy['principal'] - $payment;
                                                $extra = $payment - $last_histroy['principal'];
                                                //                                        Wallet::create([
                                                //                                            'user_id' => $loan->client_id,
                                                //                                            'amount'  => $extra,
                                                //                                            'notes'   => 'Extra amount from loan'
                                                //                                        ]);
                                            } else {
                                                $amount = 0;
                                            }
                                            $principal_posted = $last_histroy['principal'];
                                            if ($week_iteration >= ($loan_type->cap_period + $loan_type->period)) {
                                                $current_status = $loan->loan_status;
                                                $loan->update([
                                                    'loan_status' => '9',
                                                    'end_date'    => date('Y-m-d H:i:s'),
                                                ]);
                                                if ($current_status != $loan->loan_status) {
                                                    LoanApplication::addLoanStatusHistory($loan->id, '9', '', User::find(1));
                                                }
                                            } else {
                                                $current_status = $loan->loan_status;
                                                if ($week_iteration >= $loan_type->period) {
                                                    $loan->update([
                                                        'loan_status' => '8',
                                                        'end_date'    => date('Y-m-d H:i:s'),
                                                    ]);
                                                    if ($current_status != $loan->loan_status) {
                                                        LoanApplication::addLoanStatusHistory($loan->id, '8', '', User::find(1));
                                                    }
                                                } else {
                                                    $loan->update([
                                                        'loan_status' => '7',
                                                        'end_date'    => date('Y-m-d H:i:s'),
                                                    ]);
                                                    if ($current_status != $loan->loan_status) {
                                                        LoanApplication::addLoanStatusHistory($loan->id, '7', '', User::find(1));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $tax_for_origination = 0;
                        $tax = $tax_for_renewal + $tax_for_interest + $tax_for_origination;

                        $total_e_tax = $amount + $origination_fee + $interest + $renewal + $debt + $debt_collection_fee;
                        $total = $total_e_tax + $tax + $debt_tax + $debt_collection_tax;
                        $debt = round($debt, 2);
                        $debt_tax = round($debt_tax, 2);
                        $debt_collection_fee = round($debt_collection_fee, 2);
                        $debt_collection_tax = round($debt_collection_tax, 2);
                        $last_histroy = LoanCalculationHistory::create([
                            'loan_id'                      => $loan->id,
                            'week_iterations'              => $week_iteration,
                            'payment_amount'               => $payment_amount,
                            'date'                         => date('Y-m-d'),
                            'transaction_name'             => 'Payment',
                            'principal'                    => $amount,
                            'principal_posted'             => $principal_posted,
                            'origination'                  => $origination_fee,
                            'origination_posted'           => 0,
                            'interest'                     => $interest,
                            'interest_posted'              => $interest_posted,
                            'renewal'                      => $renewal,
                            'renewal_posted'               => $renewal_posted,
                            'tax_for_origination'          => $tax_for_origination,
                            'tax_for_origination_posted'   => 0,
                            'tax_for_renewal'              => $tax_for_renewal,
                            'tax_for_renewal_posted'       => $renewal_tax_posted,
                            'tax_for_interest'             => $tax_for_interest,
                            'tax_for_interest_posted'      => $interest_tax_posted,
                            'tax'                          => $tax,
                            'debt'                         => $debt,
                            'debt_posted'                  => $debt_posted,
                            'debt_tax'                     => $debt_tax,
                            'debt_tax_posted'              => $debt_tax_posted,
                            'debt_collection_value'        => $debt_collection_fee,
                            'debt_collection_value_posted' => $debt_collection_value_posted,
                            'debt_collection_tax'          => $debt_collection_tax,
                            'debt_collection_tax_posted'   => $debt_collection_tax_posted,
                            'total_e_tax'                  => $total_e_tax,
                            'total'                        => $total,
                        ]);
                        LoanTransaction::whereIn('id', $today_transaction_ids)
                            ->update([
                                'used' => $last_histroy->id,
                            ]);
                        // if ($loan->loan_status == 7 || $loan->loan_status == 8) {
                        continue;
                        // }
                    }

                }

                $origination_fee = 0;

                $payment_amount = null;

                $entry = false;
                $debt_collection_value = 0;
                $debt_collection_tax = 0;

                $debt_posted = 0;
                $debt_tax_posted = 0;
                $debt_collection_value_posted = 0;
                $debt_collection_tax_posted = 0;
                $renewal_posted = 0;
                $renewal_tax_posted = 0;
                $interest_posted = 0;
                $interest_tax_posted = 0;
                $origination_posted = 0;
                $origination_tax_posted = 0;
                $principal_posted = 0;


                if ($last_histroy == null) {
                    $debt = 0;
                    $debt_tax = 0;
                    if ($loan_type->origination_type == 1) {
                        $origination_fee = round($loan->amount * $loan_type->origination_amount / 100, 2);
                    } else {
                        $origination_fee = $loan_type->origination_amount;
                    }
                    $origination_posted = $origination_fee;

                    $amount = $loan->amount;
                    $principal_posted = $amount;

                    $interest = $loan->amount * $loan_type->interest / 100;
                    $interest_posted = $interest;

                    $renewal = 0;
                    $renewal_posted = $renewal;

                    $tax_on_origination = round($origination_fee * $loan->tax_percentage / 100, 2);
                    $origination_tax_posted = $tax_on_origination;

                    $tax_on_renewal = 0;
                    $renewal_tax_posted = $tax_on_renewal;

                    $tax_on_interest = round($interest * $loan->tax_percentage / 100, 2);
                    $interest_tax_posted = $tax_on_interest;

                    $tax = $tax_on_origination + $tax_on_renewal + $tax_on_interest;
                    $week_iteration = 0;
                    $entry = true;
                    $transaction_name = 'Loan Start';
                } else {
                    $last_histroy_week = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                        ->whereNull('payment_amount')
                        ->orderBy('id', 'desc')
                        ->first();

                    $client = User::find($loan->client_id);

                    $country = Country::find($client->country);

                    if (!config('site.cron_auto_mode') || Helper::database_date_to_current_timezone($last_histroy_week->created_at,
                            $country->timezone) <= Helper::database_date_to_current_timezone(date('Y-m-d H:i:s', strtotime('-1 week')), $country->timezone)) {
                        \Log::info('Run loan calculation' . $loan->id);
                        $transaction_name = '';
                        $week_iteration = $last_histroy['week_iterations'] + 1;
                        if ($week_iteration == ($loan_type->period)) {
                            $current_status = $loan->loan_status;
                            $loan->update([
                                'loan_status' => '5',
                            ]);
                            if ($current_status != $loan->loan_status) {
                                LoanApplication::addLoanStatusHistory($loan->id, '5', '', User::find(1));
                            }
                        }
                        $amount = $last_histroy['principal'];

                        $origination_fee = 0;

                        if ($week_iteration == 1) {
                            $interest = $last_histroy['interest'];
                        } else {
                            $interest = round($last_histroy['principal'] * $loan_type->interest / 100, 2);
                            $interest_posted = $interest;
                            $interest = $interest + $last_histroy['interest'];
                        }

                        $renewal = 0;
                        $debt = 0;
                        $debt_tax = 0;
                        $current_status = $loan->loan_status;
                        if ($week_iteration < ($loan_type->cap_period + $loan_type->period)) {
                            if ($week_iteration >= ($loan_type->period)) {
                                if ($loan_type->renewal_type == 1) {
                                    $renewal = round($last_histroy['principal'] * $loan_type->renewal_amount / 100, 2);
                                } else {
                                    $renewal = $loan_type->renewal_amount;
                                }
                                $renewal_posted = $renewal;

                            }
                        } else {
                            if ($week_iteration == ($loan_type->cap_period + $loan_type->period)) {
                                $loan->update([
                                    'loan_status' => '6',
                                ]);
                                if ($current_status != $loan->loan_status) {
                                    LoanApplication::addLoanStatusHistory($loan->id, '6', '', User::find(1));
                                }
                            }
                            if ($loan_type->debt_type == 1) {
                                $debt = ($last_histroy['total'] - $last_histroy['tax'] - $last_histroy['debt_collection_tax']) * $loan_type->debt_amount / 100;
                            } else if ($loan_type->debt_type == 2) {
                                $debt = $loan_type->debt_amount;
                            }
                            $debt_posted = $debt;

                            if ($week_iteration == ($loan_type->cap_period + $loan_type->period) && $loan_type->debt_collection_percentage != null) {
                                if ($loan_type->debt_collection_type == 1) {
                                    $debt_collection_value = ($last_histroy['total'] - $last_histroy['tax'] - $last_histroy['debt_collection_tax']) * $loan_type->debt_collection_percentage / 100;
                                } else if ($loan_type->debt_collection_type == 2) {
                                    $debt_collection_value = $loan_type->debt_collection_percentage;
                                }
                                if ($loan_type->debt_collection_tax_type == 1) {
                                    $debt_collection_tax = $debt_collection_value * $loan_type->debt_collection_tax_value / 100;
                                } else if ($loan_type->debt_collection_tax_type == 2) {
                                    $debt_collection_tax = $loan_type->debt_collection_tax_value;
                                }
                                $debt_collection_value_posted = $debt_collection_value;
                                $debt_collection_tax_posted = $debt_collection_tax;
                            } else {
                                $debt_collection_value = $last_histroy['debt_collection_value'];
                                $debt_collection_tax = $last_histroy['debt_collection_tax'];
                            }

                            if ($loan_type->debt_tax_type == 1) {
                                $debt_tax = $debt * $loan_type->debt_tax_amount / 100;
                            } else if ($loan_type->debt_tax_type == 2) {
                                $debt_tax = $loan_type->debt_tax_amount;
                            }
                            $debt_tax_posted = $debt_tax;
                            // $loan->update([
                            //     'loan_status' => '6'
                            // ]);
                            // if ($current_status != $loan->loan_status) {
                            //     LoanApplication::addLoanStatusHistory($loan->id, '6', '', User::find(1));
                            // }
                        }
                        $debt = $debt + $last_histroy['debt'];
                        $debt_tax = $debt_tax + $last_histroy['debt_tax'];
                        $renewal = $renewal + $last_histroy['renewal'];

                        $tax_on_origination = round($origination_fee * $loan->tax_percentage / 100, 2);

                        $tax_on_renewal = round($renewal * $loan->tax_percentage / 100, 2);
                        $renewal_tax_posted = $tax_on_renewal - $last_histroy['tax_for_renewal'];

                        $tax_on_interest = round($interest * $loan->tax_percentage / 100, 2);
                        $interest_tax_posted = $tax_on_interest - $last_histroy['tax_for_interest'];

                        $tax = $tax_on_origination + $tax_on_renewal + $tax_on_interest;
                        $entry = true;
                    }
                }


                if ($entry) {
                    //                if ($last_histroy == null) {
                    //                    $total_e_tax = 0;
                    //                    $total = $amount;
                    //                } else {
                    $total_e_tax = $amount + $interest + $renewal + $debt + $debt_collection_value;
                    $total = $total_e_tax + $tax + $debt_tax + $debt_collection_tax;
                    //                }

                    $debt = round($debt, 2);
                    $debt_tax = round($debt_tax, 2);
                    $debt_collection_value = round($debt_collection_value, 2);
                    $debt_collection_tax = round($debt_collection_tax, 2);
                    $date = date('Y-m-d');
                    //                if ($last_histroy == null) {
                    //                    $date = date('Y-m-d', strtotime('+1 day'));
                    //                }
                    $collector = User::find($loan->employee_id);
                    $commission = null;
                    if ($collector != null) {
                        $commission = $collector->commission;
                    }
                    LoanCalculationHistory::create([
                        'loan_id'                      => $loan->id,
                        'week_iterations'              => $week_iteration,
                        'payment_amount'               => $payment_amount,
                        'date'                         => $date,
                        'transaction_name'             => $transaction_name,
                        'principal'                    => $amount,
                        'principal_posted'             => $principal_posted,
                        'origination'                  => $origination_fee,
                        'origination_posted'           => $origination_posted,
                        'interest'                     => $interest,
                        'interest_posted'              => $interest_posted,
                        'renewal'                      => $renewal,
                        'renewal_posted'               => $renewal_posted,
                        'tax_for_origination'          => $tax_on_origination,
                        'tax_for_origination_posted'   => $origination_tax_posted,
                        'tax_for_renewal'              => $tax_on_renewal,
                        'tax_for_renewal_posted'       => $renewal_tax_posted,
                        'tax_for_interest'             => $tax_on_interest,
                        'tax_for_interest_posted'      => $interest_tax_posted,
                        'tax'                          => $tax,
                        'debt'                         => $debt,
                        'debt_posted'                  => $debt_posted,
                        'debt_tax'                     => $debt_tax,
                        'debt_tax_posted'              => $debt_tax_posted,
                        'debt_collection_value'        => $debt_collection_value,
                        'debt_collection_value_posted' => $debt_collection_value_posted,
                        'debt_collection_tax'          => $debt_collection_tax,
                        'debt_collection_tax_posted'   => $debt_collection_tax_posted,
                        'total_e_tax'                  => $total_e_tax,
                        'total'                        => $total,
                        'employee_id'                  => $loan->employee_id,
                        'commission_percent'           => $commission,
                    ]);
                }
            }
        }


        if ($this->argument('id') == 'after') {
            //after mail
            $loan_applications = LoanApplication::whereIn('loan_status', [5, 6])->get();

            foreach ($loan_applications as $key => $loan) {
                $email_history = EmailHistory::where('email_class', '=', 'App\Mail\DefaultAfterLoanReminderMail')
                    ->where('model_id', '=', $loan->id)
                    ->orderBy('id', 'desc')
                    ->first();
                $last_reminder = null;
                if ($email_history != null) {
                    $last_reminder = $email_history->created_at;
                }
                if ($last_reminder == null) {
                    $loan_status_history = LoanStatusHistory::where('loan_id', $loan->id)->where('status_id', '=', 5)->first();
                    if ($loan_status_history != null) {
                        $last_reminder = $loan_status_history->created_at;
                    }
                }
                $last_reminder = date('Y-m-d', strtotime($last_reminder));

                if ($last_reminder <= date('Y-m-d', strtotime('-15 days'))) {
                    $user = User::find($loan->client_id);
                    $outstanding_balance = 0;
                    $last_histroy = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    $loan_type = LoanType::find($loan->loan_type);
                    $loan_type->period = $loan_type->number_of_days;
                    if ($last_histroy != null) {
                        $outstanding_balance = $last_histroy->total;
                        try {
                            Mail::to($user->email)->send(new DefaultAfterLoanReminderMail($user, $loan, $outstanding_balance));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }

                        $data = [
                            'app_name'            => config('app.name'),
                            'client_name'         => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
                            'loan_id'             => $loan->id,
                            'outstanding_balance' => $outstanding_balance,
                        ];

                        $template = Template::findFromKey('loan_default_after_reminder_message', 2, $user->lang, $data);

                        $data = [];

                        FirebaseHelper::firebaseNotification($user->id, $template->subject, $template->content, 'default_after_reminder', $data);
                    }
                }
            }
        }
    }

}
