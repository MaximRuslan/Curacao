<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Credit;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CreditController extends Controller
{
    public function index()
    {
        $data = [];

        $data['wallet'] = number_format(Wallet::getUserWalletAmount(auth()->id()), 2);

        $data['get_hold_balance'] = auth()->user()->getHoldBalance();
        $data['available_balance'] = number_format($data['wallet'] - $data['get_hold_balance'], 2);

        $data['banks'] = Bank::select('banks.name', 'user_banks.account_number', 'banks.id', 'banks.transaction_fee_type', 'banks.transaction_fee', 'banks.tax_transaction')
            ->leftJoin('user_banks', function ($left) {
                $left->on('user_banks.bank_id', '=', 'banks.id')
                    ->whereNull('user_banks.deleted_at');
            })
            ->where('user_banks.user_id', '=', auth()->user()->id)
            ->get();

        $data['country'] = Country::find(auth()->user()->country);

        $data['branches'] = Branch::where('country_id', '=', auth()->user()->country)
            ->pluck('title', 'id');

        return view('client.credits', $data);
    }

    public function indexDatatable()
    {
        $credits = Credit::select('credits.*', 'banks.name as bank_name', 'branches.title as branch_name')
            ->leftJoin('banks', 'banks.id', '=', 'credits.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id')
            ->where('credits.user_id', auth()->id());
        return DataTables::of($credits)
            ->addColumn('amount', function ($data) {
                return number_format($data->amount, 2);
            })
            ->addColumn('info', function ($data) {
                $str = '';
                if ($data->payment_type == 2) {
                    $str .= 'Bank: ' . $data->bank_name . '<br>';
                    $str .= 'Transaction Charge: ' . number_format($data->transaction_charge, 2);
                } elseif ($data->payment_type == 1) {
                    $str .= 'Branch:' . $data->branch_name;
                }
                return $str;
            })
            ->addColumn('transaction_charge', function ($data) {
                return number_format($data->transaction_charge, 2);
            })
            ->addColumn('payment_type', function ($data) {
                return config('site.credit_payment_types.' . $data->payment_type);
            })
            ->addColumn('status', function ($data) {
                if ($data['status'] == 1) {
                    return 'Requested';
                } else if ($data['status'] == 2) {
                    if ($data['payment_type'] == 2) {
                        return 'In process';
                    } elseif ($data['payment_type'] == 1) {
                        return 'Approved';
                    }
                } else if ($data['status'] == 3) {
                    return 'Completed';
                } else if ($data['status'] == 4) {
                    return 'Rejected';
                }
            })
            ->addColumn('created_at', function ($data) {
                return Helper::date_time_to_current_timezone($data->created_at);
            })
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                if ($data['status'] == 1) {
                    $html .= "<a href='javascript:;' data-toggle=\"tooltip\" title='" . __('keywords.edit') . "' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                    $html .= "<a href='javascript:;' data-toggle=\"tooltip\" title='" . __('keywords.delete') . "' data-modal-id='deleteCredit' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                }
                $html .= "<a href='javascript:;' data-toggle=\"tooltip\" title='" . __('keywords.view') . "'  onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";

                $html .= "</div>";
                return $html;
            })
            ->rawColumns(['action', 'info'])
            ->make();
    }

    public function store(Request $request)
    {
        $request->request->add(['user_id' => auth()->user()->id]);
        $this->validate(request(), Credit::validationRules($request->all()));
        $id = request('id');
        $credit = Credit::find($id);
        $inputs = $request->all();
        $bank = Bank::find($inputs['bank_id']);
        $inputs['transaction_charge'] = 0;
        if ($bank != null) {
            if ($bank->transaction_fee_type == 1) {
                $inputs['transaction_charge'] = ($bank->transaction_fee * $inputs['amount'] / 100) + $bank->tax_transaction;
            } elseif ($bank->transaction_fee_type == 2) {
                $inputs['transaction_charge'] = $bank->transaction_fee + $bank->tax_transaction;
            }
        }
        if ($credit) {
            $credit->update($inputs);
        } else {
            $inputs['status'] = '1';
            Credit::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show(Credit $credit)
    {
        $filteredArr = [
            'id'                 => ["type" => "hidden", 'value' => $credit->id],
            'user_id'            => ["type" => "select", 'value' => $credit->user_id],
            'payment_type'       => ['type' => 'hidden', 'value' => $credit->payment_type],
            'amount'             => ['type' => 'text', 'value' => $credit->amount],
            'bank_id'            => ['type' => 'select', 'value' => $credit->bank_id],
            'branch_id'          => ['type' => 'select', 'value' => $credit->branch_id],
            'transaction_charge' => ['type' => 'text', 'value' => $credit->transaction_charge],
            'notes'              => ['type' => 'textarea', 'value' => $credit->notes],
            'created_at'         => [
                'type'  => 'input',
                'value' => Helper::date_time_to_current_timezone($credit->created_at)
            ],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy(Credit $credit)
    {
        $credit->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
