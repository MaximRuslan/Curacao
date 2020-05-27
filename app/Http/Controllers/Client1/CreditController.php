<?php

namespace App\Http\Controllers\Client1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Credit;
use App\Models\UserBank;
use App\Models\Wallet;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\Facades\DataTables;

class CreditController extends Controller
{
    public function index()
    {
        $data = [];

        $data['wallet'] = Wallet::getUserWalletAmount(auth()->id());

        $data['get_hold_balance'] = auth()->user()->getHoldBalance();
        $data['available_balance'] = round($data['wallet'], 2) - round($data['get_hold_balance'], 2);
        $data['wallet'] = number_format($data['wallet'], 2);
        $data['get_hold_balance'] = number_format($data['get_hold_balance'], 2);

        $data['banks'] = Bank::select('banks.name', 'user_banks.account_number', 'user_banks.id', 'banks.transaction_fee_type', 'banks.transaction_fee', 'banks.tax_transaction')
            ->leftJoin('user_banks', function ($left) {
                $left->on('user_banks.bank_id', '=', 'banks.id')
                    ->whereNull('user_banks.deleted_at');
            })
            ->where('user_banks.user_id', '=', auth()->user()->id)
            ->get();

        $data['country'] = Country::find(auth()->user()->country);

        $data['branches'] = Branch::where('country_id', '=', auth()->user()->country)
            ->pluck('title', 'id');

        return view('client1.pages.credits.index', $data);
    }

    public function store()
    {
        $data = [];
        $this->validate(request(), Credit::validationRules(request()->all()));
        $id = request('id');
        $credit = Credit::find($id);
        $inputs = request()->all();
        $bank = UserBank::find($inputs['bank_id']);
        $inputs['transaction_charge'] = 0;
        if ($bank != null) {
            $bank = Bank::find($bank->bank_id);
            if ($bank != null) {
                if ($bank->transaction_fee_type == 1) {
                    $inputs['transaction_charge'] = ($bank->transaction_fee * $inputs['amount'] / 100) + $bank->tax_transaction;
                } elseif ($bank->transaction_fee_type == 2) {
                    $inputs['transaction_charge'] = $bank->transaction_fee + $bank->tax_transaction;
                }
            }
        }
        if ($credit) {
            $credit->update($inputs);
        } else {
            $inputs['user_id'] = auth()->user()->id;
            $inputs['status'] = '1';
            Credit::create($inputs);
        }
        $data['wallet'] = Wallet::getUserWalletAmount(auth()->id());
        $data['get_hold_balance'] = auth()->user()->getHoldBalance();
        $data['available_balance'] = round($data['wallet'], 2) - round($data['get_hold_balance'], 2);
        $data['wallet'] = number_format($data['wallet'], 2);
        $data['available_balance'] = number_format($data['available_balance'], 2);
        $data['status'] = true;
        return $data;
    }

    public function indexDatatable()
    {
        $selection = [
            'credits.*',
            'banks.name as bank_name',
            'branches.title as branch_name',
            'branches.title_es as branch_name_es',
            'branches.title_nl as branch_name_nl',
        ];
        $credits = Credit::select($selection)
            ->leftJoin('user_banks', 'user_banks.id', '=', 'credits.bank_id')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id')
            ->where('credits.user_id', auth()->user()->id);
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
                    if (App::getLocale() == 'esp') {
                        $str .= 'Branch:' . $data->branch_name_es;
                    } elseif (App::getLocale() == 'pap') {
                        $str .= 'Branch:' . $data->branch_name_nl;
                    } else {
                        $str .= 'Branch:' . $data->branch_name;
                    }
                }
                return $str;
            })
            ->addColumn('branch_name', function ($row) {
                if (App::getLocale() == "esp") {
                    return $row->branch_name_es;
                } elseif (App::getLocale() == "pap") {
                    return $row->branch_name_nl;
                } else {
                    return $row->branch_name;
                }
            })
            ->addColumn('transaction_charge', function ($data) {
                return number_format($data->transaction_charge, 2);
            })
            ->addColumn('payment_type', function ($data) {
                return Lang::get('keywords.' . config('site.credit_payment_types.' . $data->payment_type));
            })
            ->addColumn('status', function ($data) {
                $status = 'Requested';
                if ($data['status'] == 1) {
                    $status = 'Requested';
                } else if ($data['status'] == 2) {
                    if ($data['payment_type'] == 2) {
                        $status = 'In process';
                    } elseif ($data['payment_type'] == 1) {
                        $status = 'Approved';
                    }
                } else if ($data['status'] == 3) {
                    $status = 'Completed';
                } else if ($data['status'] == 4) {
                    $status = 'Rejected';
                }
                return Lang::get('keywords.' . $status);
            })
            ->addColumn('created_at', function ($data) {
                return Helper::date_time_to_current_timezone($data->created_at);
            })
            ->addColumn('action', function ($data) {
                $iconClass = "btn action-button";
                $html = '<div>';
                if ($data['status'] == 1) {
                    $html .= '<a href="#nogo" data-id="' . $data->id . '" class="btn action-button editCredit"
                                data-toggle="tooltip" title="' . __('keywords.edit') . '">
                                <i class=\'material-icons\'>edit</i>
                            </a>';
                    $html .= '<a href="#nogo" data-toggle="tooltip" title="' . __('keywords.delete') . '"
                                class="btn action-button deleteCredit" data-id="' . $data->id . '">
                                <i class=\'material-icons\'>delete</i>
                            </a>';
                }
                $html .= '<a href="#nogo" data-id="' . $data->id . '" class="btn action-button editCredit"
                                data-toggle="tooltip" title="' . __('keywords.view') . '" data-type="view">
                                <i class="material-icons">remove_red_eye</i>
                            </a>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['action', 'info'])
            ->make();
    }

    public function edit(Credit $credit)
    {
        $filteredArr = [
            'id'                 => ["type" => "hidden", 'value' => $credit->id],
            'user_id'            => ["type" => "select", 'value' => $credit->user_id],
            'payment_type'       => ['type' => 'hidden', 'value' => $credit->payment_type],
            'amount'             => ['type' => 'text', 'value' => $credit->amount],
            'bank_id'            => ['type' => 'select2', 'value' => $credit->bank_id],
            'branch_id'          => ['type' => 'select2', 'value' => $credit->branch_id],
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
        $data = [];
        $credit->delete();
        $data['wallet'] = Wallet::getUserWalletAmount(auth()->id());
        $data['get_hold_balance'] = auth()->user()->getHoldBalance();
        $data['available_balance'] = round($data['wallet'], 2) - round($data['get_hold_balance'], 2);
        $data['wallet'] = number_format($data['wallet'], 2);
        $data['available_balance'] = number_format($data['available_balance'], 2);
        return $data;
    }
}
