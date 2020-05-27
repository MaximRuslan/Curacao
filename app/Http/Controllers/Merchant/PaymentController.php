<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Jobs\SendClientReceiptMerchant;
use App\Library\Helper;
use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanTransaction;
use App\Models\MerchantBranch;
use App\Models\MerchantCommission;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_merchant');
    }

    public function index()
    {
        $data = [];
        $data['branches'] = MerchantBranch::pluckListing(Helper::authMerchantUser()->id);
        return view('merchant.pages.payments.index', $data);
    }

    public function store()
    {
        $rules = [
            'loan_id' => 'required'
        ];
        $merchant = Helper::authMerchantUser();
        if ($merchant->type == 1) {
            if (!session()->has('branch_id')) {
                $rules['branch_id'] = 'required';
            }
        }
        $rules['amount'] = 'required|numeric|min:0|max:0';
        $loan = LoanApplication::find(request('loan_id'));
        if ($loan != null) {
            $history = LoanCalculationHistory::where('loan_id', '=', $loan->id)->orderBy('id', 'desc')->first();
            if ($history != null) {
                $rules['amount'] = 'required|numeric|min:0|max:' . $history->total;
            }
        }

        $this->validate(request(), $rules);

        $data = [];

        $payment_date = Helper::time_to_current_timezone(date('Y-m-d H:i:s'), null, 'Y-m-d');

        $inputs = [
            'loan_id'          => $loan->id,
            'client_id'        => $loan->client_id,
            'amount'           => request('amount'),
            'transaction_type' => '1',
            'payment_type'     => '2',
            'created_by'       => $merchant->id,
            'payment_date'     => $payment_date,
        ];

        if ($merchant->type == 1) {
            if (session()->has('branch_id')) {
                $inputs['branch_id'] = session('branch_id');
            } else {
                $inputs['branch_id'] = request('branch_id');
            }
            $inputs['merchant_id'] = $merchant->id;
        } else {
            $inputs['branch_id'] = $merchant->branch_id;
            $inputs['merchant_id'] = $merchant->merchant_id;
        }

        $commission = MerchantCommission::where('merchant_id', '=', $merchant->id)
            ->where('min_amount', '<=', request('amount'))
            ->where('max_amount', '>=', request('amount'))
            ->first();

        if ($commission != null) {
            $commission_value = request('amount') * $commission->commission / 100;

            $inputs['commission_calculated'] = $commission_value;

        }


        $transaction = LoanTransaction::create($inputs);

        $history_transactions = LoanTransaction::where('id', $transaction->id)->get();

        $history = collect([
            'date'    => $payment_date,
            'loan_id' => $loan->id
        ]);
        $main_entry = LoanCalculationHistory::calculationHistoryChange($history, 'payment', $history_transactions->pluck('id'), $payment_date);

        $this->dispatch((new SendClientReceiptMerchant($main_entry))->onQueue('client_receipt'));

        $receipt = LoanTransaction::createMerchantReceipt($main_entry);
        if (request('receipt') == 1) {
            $data['url'] = $receipt;
        }

        $data['status'] = true;

        return $data;
    }

    public function userLoan($id)
    {
        $data = [];

        $user = User::where('role_id', '=', 3)->where('id_number', '=', $id)->first();
        $status = false;
        $country = false;
        $message = __('keywords.mismatch_client_country');
        if ($user == null) {
            $loan = LoanApplication::where('id', '=', $id)->whereIn('loan_status', [4, 5, 6])->first();
        } else {
            $loan = LoanApplication::where('client_id', '=', $user->id)->whereIn('loan_status', [4, 5, 6])->orderBy('id', 'desc')->first();
        }
        if ($loan != null) {
            if ($user == null) {
                $user = User::find($loan->client_id);
            }
            if ($user != null) {
                $status = true;
                if ($user->lastname != null) {
                    $count = strlen($user->lastname);
                    $last_name = $user->lastname[0];
                    for ($i = 1; $i <= $count - 2; $i++) {
                        $last_name .= '*';
                    }
                    $last_name .= $user->lastname[$count - 1];
                } else {
                    $last_name = '';
                }
                $data['name'] = $user->firstname . ' ' . $last_name;

                $loan_history = LoanCalculationHistory::where('loan_id', '=', $loan->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($loan_history != null) {
                    $data['open_balance'] = Helper::decimalShowing($loan_history->total, $user->country);
                }
                $data['loan_id'] = $loan->id;
                $merchant = Helper::authMerchantUser();
                if ($merchant->country_id == $user->country) {
                    $country = true;
                    $message = '';
                }
            }
        }
        $data['status'] = $status;
        $data['country'] = $country;
        $data['message'] = $message;
        return $data;

    }

    public function indexDatatable()
    {
        $payments = LoanTransaction::select('loan_transactions.*', 'users.firstname', 'users.lastname', 'merchants.first_name', 'merchants.last_name', 'merchants.name as merchant',
            'merchant_branches.name as branch_name')
            ->leftJoin('users', 'users.id', '=', 'loan_transactions.client_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'loan_transactions.created_by')
            ->leftJoin('merchant_branches', 'merchant_branches.id', '=', 'loan_transactions.branch_id');

        $payments->where('loan_transactions.merchant_id', '=', Helper::getMerchantId());

        $merchant = Helper::authMerchantUser();

        if ($merchant->type == 2) {
            $payments->where('loan_transactions.created_by', '=', $merchant->id);
            $payments->where('loan_transactions.branch_id', '=', $merchant->branch_id);
        }

        if (session()->has('branch_id')) {
            $payments->where('loan_transactions.branch_id', '=', session('branch_id'));
        }

        return DataTables::of($payments)
            ->addColumn('user', function ($row) {
                $last_name = '';
                if ($row->lastname != null) {
                    $count = strlen($row->lastname);
                    $last_name = $row->lastname[0];
                    for ($i = 1; $i <= $count - 2; $i++) {
                        $last_name .= '*';
                    }
                    $last_name .= $row->lastname[$count - 1];
                }
                return $row->firstname . ' ' . $last_name;
            })
            ->addColumn('merchant', function ($row) {
                return $row->first_name . ' ' . $row->last_name . ' (' . $row->merchant . ')';
            })
            ->editColumn('created_at', function ($row) {
                return Helper::date_time_to_current_timezone($row->updated_at);
            })
            ->addIndexColumn()
            ->make(true);
    }
}
