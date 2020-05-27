<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Credit;
use App\Models\CreditStatusHistory;
use App\Models\User;
use App\Models\UserBank;
use App\Models\Wallet;
use Collective\Html\FormFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CreditController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin|processor|credit and processing');
    }

    public function index()
    {
        if (request('status') && request('type')) {
            $user = User::select(DB::raw('concat(firstname," ",lastname,"-",id_number) as name'), 'id')
                ->where('role_id', '=', 3);

            $country = session()->has('country') ? session()->get('country') : '';
            if (auth()->user()->hasRole('super admin') && $country != '') {
                $user->where(['users.country' => $country]);
            } else {
                $user->where(['users.country' => auth()->user()->country]);
            }
            $data['users'] = $user->orderBy('name', 'asc')
                ->pluck('name', 'id');

            if (auth()->user()->hasRole('super admin|admin|credit and processing')) {
                $data['branches'] = Branch::select('*');
                if (auth()->user()->hasRole('super admin')) {
                    $country = session()->has('country') ? session()->get('country') : '';
                } else {
                    $country = auth()->user()->country;
                }
                if ($country != '') {
                    $data['branches']->where('country_id', '=', $country);
                }
                $data['branches'] = $data['branches']->pluck('title', 'id');
            }

            $data['payment_types'] = config('site.payment_types');
            $data['cash_back_payment_types'] = config('site.cash_back_payment_types');
            return view('admin1.pages.credit.index', $data);
        } else {
            return redirect()->route('admin1.permission-denied');
        }
    }

    public function store()
    {
        $this->validate(request(), Credit::validationRules(request()->all()));
        $id = request('id');
        $credit = Credit::find($id);
        $inputs = request()->all();
        $bank = UserBank::find($inputs['bank_id']);
        $inputs['transaction_charge'] = 0;
        if (session()->has('branch_id')) {
            $inputs['branch_id'] = session('branch_id');
        }
        if (request()->hasFile('proof_image')) {
            $image = request()->file('proof_image');
            $name = str_slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME), '_');
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = time() . '_' . base64_encode(rand(1, 100)) . '_' . $name . '.' . $ext;
            if (!file_exists(public_path('storage/credits/'))) {
                mkdir(public_path('storage/credits/'));
            }
            $image->move(public_path('storage/credits/'), $imageName);
            $inputs['file_name'] = $imageName;
            $inputs['file_path'] = 'storage/credits/';
        }
        if ($bank != null) {
            $bank = Bank::find($bank->bank_id);
            if ($bank != null) {
                if ($bank->transaction_fee_type == 1) {
                    $inputs['transaction_charge'] = ($bank->transaction_fee * $inputs['amount'] / 100) + $bank->tax_transaction;
                } else if ($bank->transaction_fee_type == 2) {
                    $inputs['transaction_charge'] = $bank->transaction_fee + $bank->tax_transaction;
                }
            }
        }
        if ($credit) {
            $inputArray = [];
            if (isset($inputs['status']) && $inputs['status'] != '') {
                $inputArray['status'] = $inputs['status'];
                $inputArray['file_name'] = isset($inputs['file_name']) ? $inputs['file_name'] : NULL;
                $inputArray['file_path'] = isset($inputs['file_path']) ? $inputs['file_path'] : NULL;
            } else {
                $inputArray = $inputs;
            }
            $credit->update($inputArray);
        } else {
            $inputs['status'] = '1';
            $credit = Credit::create($inputs);
            if ($credit != null) {
                $credit->statusChange(1);
                $credit->sendStatusChangeMail();
            }
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
            'user_id'            => ["type" => "select2", 'value' => $credit->user_id],
            'payment_type'       => ['type' => 'select2', 'value' => $credit->payment_type],
            'amount'             => ['type' => 'text', 'value' => $credit->amount],
            'bank_id'            => ['type' => 'select2', 'value' => $credit->bank_id],
            'branch_id'          => ['type' => 'select2', 'value' => $credit->branch_id],
            'transaction_charge' => ['type' => 'text', 'value' => $credit->transaction_charge],
            'notes'              => ['type' => 'textarea', 'value' => $credit->notes],
            'wallet'             => ['type' => 'hidden', 'value' => Wallet::getUserWalletAmount($credit->user_id)],
        ];
        $banks = Bank::select('banks.name', 'user_banks.id', 'banks.transaction_fee_type', 'banks.tax_transaction', 'user_banks.account_number', 'banks.transaction_fee', 'user_banks.account_number')
            ->leftJoin('user_banks', function ($join) {
                $join->on('user_banks.bank_id', '=', 'banks.id')
                    ->whereNull('user_banks.deleted_at');
            })
            ->where('user_banks.user_id', '=', $credit->user_id)
            ->get();
        $user = User::find($credit->user_id);
        $branches = Branch::where('country_id', '=', $user->country)
            ->pluck('title', 'id');
        $wallet = Wallet::getUserWalletAmount($user->id);
        $get_hold_balance = $user->getHoldBalance($credit->id);
        $available_balance = number_format($wallet - $get_hold_balance, 2);
        return response()->json([
            "status"            => "success",
            "inputs"            => $filteredArr,
            "banks"             => $banks,
            "branches"          => $branches,
            "wallet"            => number_format($wallet, 2),
            "available_balance" => $available_balance,
        ]);
    }

    public function indexDatatable()
    {
        $country = session()->has('country') ? session()->get('country') : '';

        $credits = Credit::select([
            'credits.*',
            DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
            'users.country',
            'banks.name as bank_name',
            'user_banks.account_number',
            'branches.title as branch_name'
        ])
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'credits.user_id')->whereNull('users.deleted_at');
            })
            ->leftJoin('user_banks', 'user_banks.id', '=', 'credits.bank_id')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id');
        if (auth()->user()->hasRole('super admin')) {
            if ($country != '') {
                $credits->where('users.country', '=', $country);
            }
            $credits->whereNotNull('users.country');
        } else {
            $credits->where('users.country', '=', auth()->user()->country)
                ->whereNotNull('users.country');
        }
        if (session()->has('branch_id')) {
            $credits->where('credits.branch_id', '=', session('branch_id'));
        }
        if (request('branch_id')) {
            $credits->where('credits.branch_id', '=', request('branch_id'));
        }
        if (request('type')) {
            $credits->where('credits.payment_type', '=', request('type'));
        }
        if (request('status')) {
            $credits->where('credits.status', '=', request('status'));
        }
        return DataTables::of($credits)
            ->addColumn('credit_select', function ($data) {
                return '<label style="display:flex; height:56px;margin: 0;align-items: center; justify-content: center;">' . FormFacade::checkbox('order_select', $data->id, false, [
                        'id'    => 'checkbox_' . $data->id,
                        'class' => 'creditCheckbox'
                    ]) . '</label>';
            })
            ->setRowAttr([
                'data-id' => function ($data) {
                    return $data->id;
                }
            ])
            ->addColumn('payment_type', function ($data) {
                return config('site.credit_payment_types.' . $data->payment_type);
            })
            ->addColumn('amount', function ($data) {
                return Helper::decimalShowing($data->amount, $data->country);
            })
            ->addColumn('transaction_charge', function ($data) {
                return Helper::decimalShowing($data->transaction_charge, $data->country);
            })
            ->addColumn('status', function ($data) {
                if ($data['status'] == 1) {
                    return 'Requested';
                } else if ($data['status'] == 2) {
                    if ($data['payment_type'] == 2) {
                        return 'In process';
                    } else if ($data['payment_type'] == 1) {
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
                if (($data['status'] == 1 && auth()->user()->hasRole('admin|super admin|processor|credit and processing')) || ($data['status'] == 2 && auth()->user()->hasRole('admin|super admin'))) {
                    $html .= "<a href='javascript:;' title='Edit' data-id='$data->id' class='$iconClass editCredit'><i class='fa fa-pencil'></i></a>";
                    $html .= "<a href='javascript:;' title='Delete' data-id='$data->id' class='$iconClass deleteCredit'><i class='fa fa-trash'></i></a>";
                }
                if (($data['status'] == 1 || $data['status'] == 2) && auth()->user()->hasRole('admin|super admin|processor|credit and processing')) {
                    if ($data['status'] == 1) {
                        $title = '';
                        if ($data['payment_type'] == '1') {
                            $html .= "<a href='javascript:;' title='Approve' data-id='$data->id' class='btn btn-icon btn-sm waves-effect btn-success approveCredit'><i class='fa fa-check'></i></a>";
                        } else {
                            $html .= "<a href='javascript:;' title='In Process' data-id='$data->id' class='btn btn-icon btn-sm waves-effect btn-success inprocessCredit'><i class='fa fa-check'></i></a>";
                        }
                    }
                    if ($data['payment_type'] == 1 && $data['status'] == 2) {
                        $html .= "<a href='javascript:;' title='Complete' data-id='$data->id' class='$iconClass btn-success completeCredit'><i class='fa fa-check-square'></i></a>";
                    }
                    $html .= "<a href='javascript:;' title='Reject' data-id='$data->id' class='$iconClass btn-danger rejectCredit'><i class='fa fa-times'></i></a>";
                }
                if ($data['status'] == 3 && $data['payment_type'] == 1) {
                    $html .= "<a href='javascript:;' title='View Wallet Details' data-id='$data->id' class='$iconClass btn-danger viewWalletDetails'><i class='fa fa-eye'></i></a>";
                }
                $html .= "<a href='javascript:;' title='Status History' data-id='$data->id' class='$iconClass btn-danger statusHistory'><i class='fa fa-list-alt'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->rawColumns(['action', 'credit_select'])
            ->make();
    }

    public function destroy(Credit $credit)
    {
        $credit->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    public function statusCredits()
    {
        $this->validate(request(), [
            'id'     => 'required',
            'status' => 'required|in:2,3,4',
        ]);
        $in_status = [];
        if (request('status') == 2) {
            $in_status = [1];
        } else if (request('status') == 3) {
            $in_status = [2];
        } else if (request('status') == 4) {
            $in_status = [1, 2];
        }

        $data = [];

        $ids = explode(',', request('id'));

        $credits = Credit::select('credits.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'), 'users.country', 'banks.name as bank_name', 'branches.title as branch_name')
            ->leftJoin('users', 'users.id', '=', 'credits.user_id')
            ->leftJoin('user_banks', 'user_banks.id', '=', 'credits.bank_id')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id')
            ->whereIn('credits.status', $in_status)
            ->whereIn('credits.id', $ids)
            ->get();

        $payment_type = '';
        foreach ($credits as $key => $value) {
            if (request('status') == 3) {
                $amount = 0;
                if ($value->amount != null && $value->amount != '') {
                    $amount = $value->amount;
                }
                if ($value->payment_type == 2) {
                    $amount += $value->transaction_charge;
                }
                if ($value->payment_type == 2) {
                    Wallet::create([
                        'user_id'                  => $value->user_id,
                        'amount'                   => '-' . $amount,
                        'type'                     => '7',
                        'transaction_payment_date' => date('Y-m-d'),
                        'notes'                    => $value->id . ' credit request id',
                        'used'                     => $value->id
                    ]);
                } else {
                    if ($value->amount == request('transaction_total')['payment']) {
                        foreach (request('payment_amount') as $a_key => $a_value) {
                            if ($a_value != null && $a_value > 0) {
                                Wallet::create([
                                    'user_id'                  => $value->user_id,
                                    'amount'                   => '-' . $a_value,
                                    'type'                     => $a_key,
                                    'transaction_payment_date' => date('Y-m-d'),
                                    'notes'                    => $value->id . ' credit request id',
                                    'used'                     => $value->id
                                ]);
                            }
                        }
                        foreach (request('cashback_amount') as $ca_key => $ca_value) {
                            if ($ca_value != null && $ca_value > 0) {
                                Wallet::create([
                                    'user_id'                  => $value->user_id,
                                    'amount'                   => $ca_value,
                                    'type'                     => $ca_key,
                                    'transaction_payment_date' => date('Y-m-d'),
                                    'notes'                    => $value->id . ' credit request id',
                                    'used'                     => $value->id
                                ]);
                            }
                        }
                    } else {
                        $data['status'] = false;
                        $data['message'] = 'Cash pay out amount should be same as payment total amount';
                        return $data;
                    }
                }
            }
            $value->update([
                'status' => request('status')
            ]);
            $value->statusChange(request('status'), request('notes'));

            $value->sendStatusChangeMail();

            $payment_type = $value->payment_type;
        }
        if ((request('status') == 2 || request('status') == 3 || request('status') == 4) && $credits->count() > 0) {
            $status = '';
            if (request('status') == 2) {
                if ($payment_type == 2) {
                    $status = 'In process';
                } else if ($payment_type == 1) {
                    $status = 'Approved';
                }
            } else if (request('status') == 3) {
                $status = 'Completed';
            } else if (request('status') == 4) {
                $status = 'Rejected';
            }
            $data['url'] = self::excelCreation($credits, $status);
        }
        $data['status'] = true;
        return $data;
    }

    public function excelCreation($result, $status)
    {
        $data = [];
        foreach ($result as $key => $value) {
            $data[$key]['User Name'] = ucwords(strtolower($value->user_name));
            $data[$key]['Type'] = config('site.credit_payment_types.' . $value->payment_type);
            $data[$key]['Amount'] = Helper::decimalShowing($value->amount, $value->country);
            if ($value->payment_type == 2) {
                $data[$key]['Bank'] = ucwords(strtolower($value->bank_name));
                $data[$key]['Bank Transaction Charge'] = Helper::decimalShowing($value->transaction_charge, $value->country);
            } else {
                $data[$key]['Branch'] = ucwords(strtolower($value->branch_name));
            }
            $data[$key]['Notes'] = $value->notes;
            $data[$key]['Date'] = Helper::date_time_to_current_timezone($value->created_at);
        }
        $filename = $status . '-' . date('Ymd') . '-' . time();
        Excel::create($filename, function ($excel) use ($data) {
            $excel->setTitle('Report OF ' . date('d-m-Y H:i:s'));
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx', public_path() . '/uploads/excel');

        return URL::to('/') . '/uploads/excel/' . $filename . '.xlsx';
    }

    public function csvExport()
    {
        $data = [];
        $credits = Credit::select('credits.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'), 'users.country',
            'banks.name as bank_name', 'branches.title as branch_name')
            ->leftJoin('users', 'users.id', '=', 'credits.user_id')
            ->leftJoin('user_banks', 'user_banks.id', '=', 'credits.bank_id')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->leftJoin('branches', 'branches.id', '=', 'credits.branch_id')
            ->where('credits.status', '=', request('status'))
            ->where('credits.payment_type', '=', request('type'));
        if (request('values')) {
            $credits->whereIn('credits.id', request('values'));
        }
        $credits = $credits->get();
        $data['url'] = self::excelCreation($credits, 'Credits');
        return $data;
    }

    public function statusHistory(Credit $credit)
    {
        $data = [];
        $data['history'] = CreditStatusHistory::select('credit_status_histories.*', DB::raw('concat(users.firstname," ",users.lastname) as user_name'))
            ->leftJoin('users', 'users.id', '=', 'credit_status_histories.user_id')
            ->where('credit_status_histories.credit_id', '=', $credit->id)
            ->get();
        $data['history'] = $data['history']->map(function ($item, $key) use ($credit) {
            $item->user_name = ucwords(strtolower($item->user_name));
            if ($item->status_id == 1) {
                $item->status = 'Requested';
            } else if ($item->status_id == 2) {
                if ($credit->payment_type == 2) {
                    $item->status = 'In Process';
                } else if ($credit->payment_type == 1) {
                    $item->status = 'Approved';
                }
            } else if ($item->status_id == 3) {
                $item->status = 'Completed';
            } else if ($item->status_id == 4) {
                $item->status = 'Rejected';
            }
            $item->date = Helper::date_time_to_current_timezone($item->created_at);
            return $item;
        });
        return $data;
    }

    public function walletHistory($credit)
    {
        $data = [];
        $data['amount'] = Wallet::where('used', '=', $credit)
            ->where('amount', '<', 0)
            ->pluck('amount', 'type');
        $data['cashback_amount'] = Wallet::where('used', '=', $credit)
            ->where('amount', '>', 0)
            ->pluck('amount', 'type');
        return $data;
    }
}
