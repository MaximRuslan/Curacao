<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class LoanCalculationHistory extends BaseModel
{

    use SoftDeletes;

    protected $fillable = [
        'payment_amount',
        'week_iterations',
        'loan_id',
        'date',
        'transaction_name',
        'principal',
        'principal_posted',
        'origination',
        'origination_posted',
        'interest',
        'interest_posted',
        'renewal',
        'renewal_posted',
        'tax',
        'tax_for_origination',
        'tax_for_origination_posted',
        'tax_for_renewal',
        'tax_for_renewal_posted',
        'tax_for_interest',
        'tax_for_interest_posted',
        'debt',
        'debt_posted',
        'debt_tax',
        'debt_tax_posted',
        'debt_collection_value',
        'debt_collection_value_posted',
        'debt_collection_tax',
        'debt_collection_tax_posted',
        'total_e_tax',
        'total',
        'employee_id',
        'commission_percent',
        'commission',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
    ];

    public static function calculationHistoryManual($history, $payment_ids, $inputs, $date = null)
    {

        $loan_transactions = LoanTransaction::whereIn('id', $payment_ids)->get();

        $payment_amount = $loan_transactions->sum('amount') - $loan_transactions->sum('cash_back_amount');

        $total_e_tax = round($inputs['principal'] + $inputs['interest'] + $inputs['renewal'] + $inputs['debt'] + $inputs['debt_collection_value'], 2);
        $total = round($total_e_tax + $inputs['tax_for_renewal'] + $inputs['tax_for_interest'] + $inputs['debt_tax'] + $inputs['debt_collection_tax'], 2);
        $last_histroy = LoanCalculationHistory::create([
            'loan_id'               => $history->loan_id,
            'week_iterations'       => $history->week_iterations,
            'payment_amount'        => $payment_amount,
            'date'                  => $date,
            'transaction_name'      => 'Payment',
            'principal'             => $inputs['principal'],
            'origination'           => 0,
            'interest'              => $inputs['interest'],
            'renewal'               => $inputs['renewal'],
            'tax_for_origination'   => 0,
            'tax_for_renewal'       => round($inputs['tax_for_renewal'], 2),
            'tax_for_interest'      => round($inputs['tax_for_interest'], 2),
            'tax'                   => round($inputs['tax_for_renewal'] + $inputs['tax_for_interest'], 2),
            'debt'                  => $inputs['debt'],
            'debt_tax'              => $inputs['debt_tax'],
            'debt_collection_value' => $inputs['debt_collection_value'],
            'debt_collection_tax'   => $inputs['debt_collection_tax'],
            'total_e_tax'           => $total_e_tax,
            'total'                 => $total,
        ]);
        LoanTransaction::whereIn('id', $payment_ids)->update([
            'used' => $last_histroy->id,
        ]);
    }

    public static function calculationHistoryChange($history, $type, $payment_ids, $date = null, $write_off = 'false')
    {
        info('$history');
        info($history);
        $last_histroy = LoanCalculationHistory::where('loan_id', '=', $history['loan_id'])
            //            ->where('week_iterations', '<', $history->week_iterations)
            ->orderBy('id', 'desc')
            ->first();

        $loan = LoanApplication::where('id', '=', $history['loan_id'])->first();

        $loan_type = LoanType::find($loan->loan_type);

        $loan_type->period = $loan_type->number_of_days;

        if ($type == 'payment') {

            $loan_transactions = LoanTransaction::whereIn('id', $payment_ids)->get();

            $payment = $payment_amount = $loan_transactions->sum('amount') - $loan_transactions->sum('cash_back_amount');

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

            if ($week_iteration >= ($loan_type->period)) {
                if ($week_iteration >= ($loan_type->cap_period + $loan_type->period)) {
                    if (!LoanStatusHistory::historyHas($loan->id, [6])) {
                        $current_status = $loan->loan_status;
                        $loan->update([
                            'loan_status' => '6',
                            'end_date'    => null,
                        ]);
                        if ($current_status != $loan->loan_status) {
                            LoanApplication::addLoanStatusHistory($loan->id, '6', null, null, $last_histroy->created_at);
                        }
                    }
                } else {
                    if (!LoanStatusHistory::historyHas($loan->id, [5])) {
                        $current_status = $loan->loan_status;
                        $loan->update([
                            'loan_status' => '5',
                            'end_date'    => null,
                        ]);
                        if ($current_status != $loan->loan_status) {
                            LoanApplication::addLoanStatusHistory($loan->id, '5', null, null, $last_histroy->created_at);
                        }
                    }
                }
            } else {
                $current_status = $loan->loan_status;
                $loan->update([
                    'loan_status' => '4',
                    'end_date'    => null,
                ]);
                if ($current_status != $loan->loan_status) {
                    LoanApplication::addLoanStatusHistory($loan->id, '4', null, null, $last_histroy->created_at);
                }
            }

            if ($debt + $debt_tax > $payment) {
                $old_debt = $debt;
                $old_debt_tax = $debt_tax;
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
                    $old_debt_collection_value = $debt_collection_fee;
                    $old_debt_collection_tax = $debt_collection_tax;
                    $remaing_total_debt_collection = $debt_collection_tax + $debt_collection_fee - $payment;
                    $portion_debt_collection_tax = 1 + ($loan->tax_percentage / 100);
                    $debt_collection_fee = round($remaing_total_debt_collection / $portion_debt_collection_tax, 2);
                    $debt_collection_tax = $remaing_total_debt_collection - $debt_collection_fee;
                    $debt_collection_value_posted = $old_debt_collection_value - $debt_collection_fee;
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
                            $amount = round($last_histroy['principal'], 2) - round($payment, 2);
                            if ($payment >= $last_histroy['principal']) {
                                $extra = $payment - $last_histroy['principal'];
                                //                            Wallet::create([
                                //                                'user_id' => $loan->client_id,
                                //                                'amount'  => $extra,
                                //                                'notes'   => 'Extra amount from loan'
                                //                            ]);
                                $principal_posted = $last_histroy['principal'];
                            } else {
                                $principal_posted = $payment;
                            }
                            if ($amount <= 0) {
                                $current_status = $loan->loan_status;
                                if ($week_iteration >= ($loan_type->cap_period + $loan_type->period)) {
                                    $loan->update([
                                        'loan_status' => '9',
                                        'end_date'    => date('Y-m-d H:i:s'),
                                    ]);
                                    if ($current_status != $loan->loan_status) {
                                        if (isset($history->created_at)) {
                                            LoanApplication::addLoanStatusHistory($loan->id, '9', null, null, $history->created_at);
                                        } else {
                                            LoanApplication::addLoanStatusHistory($loan->id, '9', null, null);
                                        }
                                    }
                                } else {
                                    if ($week_iteration >= $loan_type->period) {
                                        $loan->update([
                                            'loan_status' => '8',
                                            'end_date'    => date('Y-m-d H:i:s'),
                                        ]);
                                        if ($current_status != $loan->loan_status) {
                                            if (isset($history->created_at)) {
                                                LoanApplication::addLoanStatusHistory($loan->id, '8', null, null, $history->created_at);
                                            } else {
                                                LoanApplication::addLoanStatusHistory($loan->id, '8', null, null);
                                            }
                                        }
                                    } else {
                                        if ($loan->isFirstLoan()) {
                                            ReferralHistory::storeHistory($loan, 2);
                                        }
                                        $loan->update([
                                            'loan_status' => '7',
                                            'end_date'    => date('Y-m-d H:i:s'),
                                        ]);
                                        if ($current_status != $loan->loan_status) {
                                            if (isset($history->created_at)) {
                                                LoanApplication::addLoanStatusHistory($loan->id, '7', null, null, $history->created_at);
                                            } else {
                                                LoanApplication::addLoanStatusHistory($loan->id, '7', null, null);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($write_off == 'true') {
                $loan->update([
                    'loan_status' => '10',
                    'end_date'    => date('Y-m-d H:i:s'),
                ]);
                LoanApplication::addLoanStatusHistory($loan->id, '10');
            }
            $tax_for_origination = 0;
            $tax = $tax_for_renewal + $tax_for_interest + $tax_for_origination;

            $total_e_tax = $amount + $origination_fee + $interest + $renewal + $debt + $debt_collection_fee;
            $total = $total_e_tax + $tax + $debt_tax + $debt_collection_tax;

            if ($date == null) {
                $date = $history->date;
            }

            if ($week_iteration == null) {
                $week_iteration = 0;
            }

            $debt = round($debt, 2);
            $debt_tax = round($debt_tax, 2);
            $debt_collection_fee = round($debt_collection_fee, 2);
            $debt_collection_tax = round($debt_collection_tax, 2);

            $transaction_name = 'Payment';
            if ($write_off == 'true') {
                $total = $total_e_tax = 0;
                $transaction_name = 'Write off';
            }

            $last_histroy = LoanCalculationHistory::create([
                'loan_id'                      => $loan->id,
                'week_iterations'              => $week_iteration,
                'payment_amount'               => $payment_amount,
                'date'                         => $date,
                'transaction_name'             => $transaction_name,
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
                'employee_id'                  => $history['employee_id'],
                'commission_percent'           => $history['commission_percent'],
                'commission'                   => $history['commission'],
            ]);
            $wallet = null;
            if (isset($history['id'])) {
                $wallet = Wallet::where('history_id', '=', $history['id'])->first();
            }

            $inputs = [
                'amount'                   => $history['commission'],
                'transaction_payment_date' => $date,
                'history_id'               => $last_histroy->id,
            ];
            if ($wallet != null) {
                $wallet->update($inputs);
            } else {
                $inputs['user_id'] = $history['employee_id'];
                Wallet::create($inputs);
            }

            LoanTransaction::whereIn('id', $payment_ids)
                ->update([
                    'used' => $last_histroy->id,
                ]);
            return $last_histroy;
        }
        if ($type == 'week') {
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

                //                    if($last_histroy->date==date('Y-m-d','-1 week')){
                //
                //                    }

                $transaction_name = '';
                $week_iteration = $last_histroy['week_iterations'] + 1;
                $current_status = $loan->loan_status;
                if ($week_iteration >= ($loan_type->period)) {
                    if (!LoanStatusHistory::historyHas($loan->id, [5, 7, 8, 9])) {
                        $loan->update([
                            'loan_status' => '5',
                            'end_date'    => null,
                        ]);
                        if ($current_status != $loan->loan_status) {
                            LoanApplication::addLoanStatusHistory($loan->id, '5', null, null, $history->created_at);
                        }
                    }
                } else {
                    if (!LoanStatusHistory::historyHas($loan->id, [7, 8, 9])) {
                        $loan->update([
                            'loan_status' => '4',
                            'end_date'    => null,
                        ]);
                        if ($current_status != $loan->loan_status) {
                            LoanApplication::addLoanStatusHistory($loan->id, '4', null, null, $history->created_at);
                        }
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
                        if ($loan->renewal_type == 1) {
                            $renewal = round($last_histroy['principal'] * $loan_type->renewal_amount / 100, 2);
                        } else {
                            $renewal = $loan_type->renewal_amount;
                        }
                        $renewal_posted = $renewal;
                    }
                } else {
                    if ($week_iteration == ($loan_type->cap_period + $loan_type->period)) {
                        if (!LoanStatusHistory::historyHas($loan->id, [6, 7, 8, 9])) {
                            $loan->update([
                                'loan_status' => '6',
                            ]);
                            if ($current_status != $loan->loan_status) {
                                LoanApplication::addLoanStatusHistory($loan->id, '6', null, null, $history->created_at);
                            }
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
                }
                $debt = $debt + $last_histroy['debt'];
                $debt_tax = $debt_tax + $last_histroy['debt_tax'];
                $renewal = $renewal + $last_histroy['renewal'];

                $tax_on_origination = round($origination_fee * $loan->tax_percentage / 100, 2);
                $tax_on_renewal = round($renewal * $loan->tax_percentage / 100, 2);
                Log::info('tax on renewal' . $tax_on_renewal);
                Log::info('tax on renewal last' . $tax_on_renewal);
                $renewal_tax_posted = $tax_on_renewal - $last_histroy['tax_for_renewal'];
                Log::info('diff' . $renewal_tax_posted);

                $tax_on_interest = round($interest * $loan->tax_percentage / 100, 2);
                $interest_tax_posted = $tax_on_interest - $last_histroy['tax_for_interest'];

                $tax = $tax_on_origination + $tax_on_renewal + $tax_on_interest;
                $entry = true;
            }


            if ($entry) {
                //                if ($last_histroy == null) {
                //                    $total_e_tax = 0;
                //                    $total = $amount;
                //                } else {
                $total_e_tax = $amount + $interest + $renewal + $debt + $debt_collection_value;
                $total = $total_e_tax + $tax + $debt_tax;
                //                }

                if ($week_iteration == null) {
                    $week_iteration = 0;
                }
                $debt = round($debt, 2);
                $debt_tax = round($debt_tax, 2);
                $debt_collection_value = round($debt_collection_value, 2);
                $debt_collection_tax = round($debt_collection_tax, 2);
                LoanCalculationHistory::create([
                    'loan_id'                      => $loan->id,
                    'week_iterations'              => $week_iteration,
                    'payment_amount'               => $payment_amount,
                    'date'                         => $history->date,
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
                    'employee_id'                  => $history['employee_id'],
                    'commission_percent'           => $history['commission_percent'],
                    'commission'                   => $history['commission'],
                    'created_at'                   => $history->created_at,
                ]);
            }
        }
    }

    public static function changeStatusAsPerLast($loan_id)
    {
        $loan = LoanApplication::find($loan_id);
        $last_history = LoanCalculationHistory::where('loan_id', '=', $loan_id)->orderBy('id', 'desc')->first();
        $loan_type = LoanType::find($loan->loan_type);
        $week_iteration = $last_history['week_iterations'];
        if ($week_iteration >= ($loan_type->period)) {
            if ($week_iteration >= ($loan_type->cap_period + $loan_type->period)) {
                $current_status = $loan->loan_status;
                $loan->update([
                    'loan_status' => '6',
                    'end_date'    => null,
                ]);
                if ($current_status != $loan->loan_status) {
                    LoanApplication::addLoanStatusHistory($loan->id, '6');
                }
            } else {
                $current_status = $loan->loan_status;
                $loan->update([
                    'loan_status' => '5',
                    'end_date'    => null,
                ]);
                if ($current_status != $loan->loan_status) {
                    LoanApplication::addLoanStatusHistory($loan->id, '5');
                }
            }
        } else {
            $current_status = $loan->loan_status;
            $loan->update([
                'loan_status' => '4',
                'end_date'    => null,
            ]);
            if ($current_status != $loan->loan_status) {
                LoanApplication::addLoanStatusHistory($loan->id, '4');
            }
        }
    }

}
