<?php

namespace App\Models;

use App\Library\EmailHelper;
use App\Library\FirebaseHelper;
use App\Library\Helper;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class LoanTransaction extends BaseModel
{

    //
    protected $fillable = [
        'client_id',
        'loan_id',
        'transaction_type',
        'transaction_payment_date',
        'next_payment_date',
        'payment_type',
        'notes',
        'amount',
        'created_by',
        'used',
        'payment_date',
        'next_payment_date',
        'cash_back_amount',
        'branch_id',
        'reconciled_at',
        'calculated_amount',
        'commission_calculated',
        'merchant_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function type()
    {
        return $this->hasOne(TransactionType::class, 'id', 'transaction_type');
    }

    public static function createReceipt($main_entry, $branch_id = null, $write_off = 'false')
    {
        $loan = LoanApplication::find($main_entry->loan_id);
        $loan_transactions = LoanTransaction::where('used', '=', $main_entry->id)
            ->orderBy('payment_type', 'asc')
            ->get();

        $receipt_id = '';

        $client_id = $loan->client_id;
        $date = date('Y-m-d');
        if (isset($loan_transactions[0])) {
            $client_id = $loan_transactions[0]->client_id;
            $branch_id = $loan_transactions[0]->branch_id;
            $date = $loan_transactions[0]->payment_date;
        }

        $client = User::find($client_id);

        $country = Country::find($client->country);

        $receipt_id .= sprintf("%'02d", $country->id);

        $branch = Branch::find($branch_id);

        $branch_name = '';
        if ($branch != null) {
            $branch_name = $branch->title;
        }

        $receipt_id .= sprintf("%'03d", $branch_id);

        $receipt_id .= $main_entry->id;

        EmailHelper::emailConfigChanges($country->mail_smtp);

        $loan_transactions_change = $loan_transactions->where('amount', '>', 0)->where('transaction_type', '=', 1)->pluck('payment_type');
        $loan_transaction = [];
        foreach ($loan_transactions_change as $key => $value) {
            if (config('site.payment_types.' . $value) == 'Vault' || config('site.payment_types.' . $value) == 'Vault door') {
                $loan_transaction[] = "Cash";
            } else {
                $loan_transaction[] = config('site.payment_types.' . $value);
            }
        }
        $loan_transaction = collect($loan_transaction)->unique()->toArray();
        $loan_transaction = implode(',', $loan_transaction);

        $data = [
            'image'               => asset('uploads/' . $country->logo),
            'lang'                => $client->lang,
            'country_name'        => $country->name,
            'name'                => $country->company_name,
            'web'                 => $country->web,
            'telephone'           => $country->telephone,
            'branch_name'         => $branch_name,
            'date'                => Helper::datebaseToFrontDate($date),
            'receipt_id'          => $receipt_id,
            'client_name'         => $client->firstname . ' ' . $client->lastname,
            'principal'           => Helper::decimalShowing($main_entry->principal, $country->id),
            'interest'            => Helper::decimalShowing($main_entry->interest, $country->id),
            'origination'         => Helper::decimalShowing($main_entry->origination, $country->id),
            'renewal'             => Helper::decimalShowing($main_entry->renewal, $country->id),
            'debt_collection_fee' => Helper::decimalShowing($main_entry->debt_collection_value, $country->id),
            'admin_fee'           => Helper::decimalShowing($main_entry->debt, $country->id),
            'tax'                 => Helper::decimalShowing($main_entry->tax_for_origination + $main_entry->tax_for_renewal + $main_entry->tax_for_interest + $main_entry->debt_tax + $main_entry->debt_collection_tax,
                $country->id),
            'payment_amount'      => Helper::decimalShowing($main_entry->payment_amount, $country->id),
            'loan_transaction'    => $loan_transaction,
            'currency'            => $country->valuta_name,
            'total'               => Helper::decimalShowing($main_entry->total, $country->id),
        ];


        if ($write_off == 'true') {
            $before_entry = LoanCalculationHistory::where('loan_id', '=', $main_entry->loan_id)->where('id', '<', $main_entry->id)->orderBy('id', 'desc')->first();
            $data['principal'] = Helper::decimalShowing($before_entry->principal, $country->id);
            $data['interest'] = Helper::decimalShowing($before_entry->interest, $country->id);
            $data['origination'] = Helper::decimalShowing($before_entry->origination, $country->id);
            $data['renewal'] = Helper::decimalShowing($before_entry->renewal, $country->id);
            $data['debt_collection_fee'] = Helper::decimalShowing($before_entry->debt_collection_value, $country->id);
            $data['admin_fee'] = Helper::decimalShowing($before_entry->debt, $country->id);
            $data['tax'] = Helper::decimalShowing($before_entry->tax_for_origination + $before_entry->tax_for_renewal + $before_entry->tax_for_interest + $before_entry->debt_tax + $before_entry->debt_collection_tax,
                $country->id);
            $data['due_amount'] = Helper::decimalShowing($before_entry->total, $country->id);
            $data['deduction'] = Helper::decimalShowing($before_entry->total - $data['payment_amount'], $country->id);
            $data['lang'] = 'esp';
            $html = View::make('admin1/pages/loans/write_off_receipt', $data);
        } else {
            $html = View::make('admin1/pages/loans/transaction_receipt', $data);
        }

        $file = time() . $client->id . '_receipt.pdf';

        Browsershot::html($html)->save(public_path('pdf/' . $file));

        return asset('pdf/' . $file);
    }

    public static function createMerchantReceipt($main_entry)
    {
        $loan_transactions = LoanTransaction::where('used', '=', $main_entry->id)
            ->orderBy('payment_type', 'asc')
            ->get();

        $merchant = Merchant::find($loan_transactions[0]->merchant_id);
        $branch = MerchantBranch::find($loan_transactions[0]->branch_id);
        $created_by = Merchant::find($loan_transactions[0]->created_by);

        $lang = 'eng';
        $merchant_name = '';
        $branch_name = '';
        $subuser_name = '';
        if ($merchant != null) {
            $merchant_name = $merchant->name;
        }
        if ($branch != null) {
            $branch_name = $branch->name;
        }
        if ($created_by != null) {
            $subuser_name = $created_by->first_name . ' ' . $created_by->last_name;
            $lang = $created_by->lang;
        }

        $receipt_id = '';

        $client = User::find($loan_transactions[0]->client_id);

        $country = Country::find($client->country);

        $receipt_id .= sprintf("%'02d", $country->id);

        $branch = Branch::find($loan_transactions[0]->branch_id);

        $receipt_id .= sprintf("%'03d", $branch->id);

        $receipt_id .= $main_entry->id;

        EmailHelper::emailConfigChanges($country->mail_smtp);

        $loan_transactions_change = $loan_transactions->where('amount', '>', 0)->where('transaction_type', '=', 1)->pluck('payment_type');
        $loan_transaction = [];
        foreach ($loan_transactions_change as $key => $value) {
            if (config('site.payment_types.' . $value) == 'Vault' || config('site.payment_types.' . $value) == 'Vault door') {
                $loan_transaction[] = "Cash";
            } else {
                $loan_transaction[] = config('site.payment_types.' . $value);
            }
        }
        $loan_transaction = collect($loan_transaction)->unique()->toArray();
        $loan_transaction = implode(',', $loan_transaction);

        $last_name = '';
        if ($client->lastname != null) {
            $count = strlen($client->lastname);
            $last_name = $client->lastname[0];
            for ($i = 1; $i <= $count - 2; $i++) {
                $last_name .= '*';
            }
            $last_name .= $client->lastname[$count - 1];
        }

        $data = [
            'image'            => asset('uploads/' . $country->logo),
            'lang'             => $lang,
            'merchant_name'    => $merchant_name,
            'branch_name'      => $branch_name,
            'subuser_name'     => $subuser_name,
            'country_name'     => $country->name,
            'name'             => $country->company_name,
            'web'              => $country->web,
            'telephone'        => $country->telephone,
            'date'             => Helper::datebaseToFrontDate($loan_transactions[0]->payment_date),
            'receipt_id'       => $receipt_id,
            'client_name'      => $client->firstname . ' ' . $last_name,
            'payment_amount'   => $main_entry->payment_amount,
            'loan_transaction' => $loan_transaction,
            'currency'         => $country->valuta_name,
            'total'            => $main_entry->total,
        ];

        $html = View::make('admin1/pages/merchants/transaction_receipt', $data);

        $file = time() . $client->id . '_receipt.pdf';

        Browsershot::html($html)->save(public_path('pdf/' . $file));

        return asset('pdf/' . $file);
    }

    public static function sendMailAndNotification($loan, $receipt)
    {
        $user = User::find($loan->client_id);
        try {
            Mail::to($user->email)->send(new PaymentConfirmationMail($user, $loan, $receipt));
        } catch (\Exception $e) {
            Log::error($e);
        }

        $data = [
            'app_name'    => config('app.name'),
            'client_name' => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
        ];

        $key = 'payment_confirmation_message';

        $template = Template::findFromKey($key, 2, $user->lang, $data);

        $data = [
            'receipt' => $receipt,
        ];

        FirebaseHelper::firebaseNotification($user->id, $template->subject, $template->content, 'payment_confirm', $data);
    }

    public static function findRemainingAmount($merchant_id, $branch_id, $except_commissions = [], $except_reconciliations = [], $status = [2])
    {
        $subusers = Merchant::where('merchant_id', '=', $merchant_id)->pluck('id');
        $collected = LoanTransaction::where('merchant_id', '=', $merchant_id)
            ->where('branch_id', '=', $branch_id)
            ->whereNotIn('id', $except_commissions)
            ->whereNotNull('commission_calculated')
            ->sum('amount');
        $commission = LoanTransaction::where('merchant_id', '=', $merchant_id)
            ->where('branch_id', '=', $branch_id)
            ->whereNotIn('id', $except_commissions)
            ->whereNotNull('commission_calculated')
            ->sum('commission_calculated');
        $reconciled = MerchantReconciliation::where(function ($query) use ($merchant_id, $subusers) {
            $query->whereIn('merchant_id', $subusers)->orWhere('merchant_id', '=', $merchant_id);
        })
            ->where('branch_id', '=', $branch_id)
            ->whereNotIn('id', $except_reconciliations)
            ->whereIn('status', $status)
            ->sum('amount');
        return Helper::decimalRound2($collected - $commission - $reconciled);
    }

}
