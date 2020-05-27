<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Mail\ConfirmMerchantEmail;
use App\Models\Country;
use App\Models\LoanTransaction;
use App\Models\Merchant;
use App\Models\MerchantBranch;
use App\Models\MerchantCommission;
use App\Models\MerchantDetail;
use App\Models\MerchantReconciliation;
use App\Models\MerchantReconciliationHistory;
use App\Models\UserStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin');
    }

    public function index()
    {
        return view('admin1.pages.merchants.index');
    }

    public function create()
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';
        $data['countries'] = Country::pluckListing(auth()->user(), $country);
        $data['statuses'] = UserStatus::pluckListing([], 'merchant');
        $data['types'] = config('site.merchant_types');
        $data['merchants'] = Merchant::pluckListing(1, auth()->user());
        $data['lang'] = config('site.language');
        return view('admin1.pages.merchants.create', $data);
    }

    public function store()
    {
        $data = [];
        $user = auth()->user();
        $id = null;
        $merchant = null;
        $inputs = request()->all();
        if (isset($inputs['id'])) {
            $merchant = Merchant::find($inputs['id']);
            if ($merchant != null) {
                $id = $merchant->id;
            }
        }
        $this->validate(request(), Merchant::validationRules($inputs, $user, $id), Merchant::validationMessages($inputs));

        if ($inputs['type'] == 1) {
            $errors = Merchant::commissionValidation(request('min_amount'), request('max_amount'));

            if (count($errors) > 0) {
                return response()->json([
                    'errors'  => $errors,
                    'message' => 'The given data was invalid.'
                ], 422);
            }
        }

        $types = [
            'type',
            'country_id',
            'first_name',
            'last_name',
            'lang',
            'status',
            'reconciliation'
        ];

        if ($inputs['type'] == 1) {
            $types[] = 'name';
            $types[] = 'tax_id';
        } else if ($inputs['type'] == 2) {
            $types[] = 'merchant_id';
            $types[] = 'branch_id';
            $types[] = 'email';
        }
        $inputs = request()->only($types);

        $inputs['reconciliation'] = 0;
        if (request('reconciliation') == 1) {
            $inputs['reconciliation'] = 1;
        }

        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $inputs['country_id'] = session('country');
            }
        } else {
            $inputs['country_id'] = auth()->user()->country;
        }
        if ($inputs['type'] == 2) {
            $merchant_sub = Merchant::find($inputs['merchant_id']);
            $inputs['country_id'] = $merchant_sub->country_id;
        }
        $create = false;
        $send_again = false;
        if ($merchant != null) {
            if ($inputs['type'] == 2 && $merchant->email != $inputs['email']) {
                $send_again = true;
                $inputs['is_verified'] = 0;
            }
            $merchant->update($inputs);
        } else {
            $merchant = Merchant::create($inputs);
            $create = true;
        }

        if ($inputs['type'] == 1) {
            $merchant->saveBranches(request('branches'), request('branch_id'));

            $merchant->saveEmails(request('secondary_email'), request('secondary_email_id'), request('primary'));

            $merchant->saveCommission(request('min_amount'), request('max_amount'), request('commission'), request('commission_id'));

            $merchant->saveTelephone(request('telephone'), request('telephone_id'), request('telephone_primary'));
        } else if ($inputs['type'] == 2) {
            $merchant_detail = MerchantDetail::where('merchant_id', '=', $merchant->id)->where('type', '=', 1)->first();
            $ids = [];
            if ($merchant_detail != null) {
                $ids[] = $merchant_detail->id;
            }
            if ($create) {
                $ids = [''];
            }
            $merchant->saveEmails([$inputs['email']], $ids, 0);
        }
        if ($create || $send_again) {
            $user_info = MerchantDetail::where('merchant_id', '=', $merchant->id)
                ->where('type', '=', 1)
                ->where('primary', '=', 1)
                ->first();

            $password = '';
            if ($user_info->is_verified == 0) {
                $password = $merchant->createPassword();
                $merchant->update([
                    'password' => bcrypt($password),
                ]);
            }

            try {
                Mail::to($user_info->value)->send(new ConfirmMerchantEmail($merchant, $user_info->id, $user_info->value, $password));
            } catch (\Exception $e) {
                Log::error($e);
            }
            Log::info('verification mail sent to ' . $user_info->value . '.');
        }

        $data['status'] = true;
        return $data;
    }

    public function indexDatatable()
    {
        $selection = [
            'merchants.*',
            'countries.name as country_name',
            'user_status.title as status_name',
            //            DB::raw('(select sum(amount) from wallets where wallets.user_id=users.id and wallets.deleted_at is null) as wallet'),
            'me.name as merchant_name'
        ];
        $merchants = Merchant::select($selection)
            ->leftJoin('merchants as me', 'me.id', '=', 'merchants.merchant_id')
            ->leftJoin('countries', 'countries.id', '=', 'merchants.country_id')
            ->leftJoin('user_status', 'user_status.id', '=', 'merchants.status')
            ->groupBy('merchants.id');

        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $merchants->where('merchants.country_id', '=', $country);
            }
        } else {
            $merchants->where('merchants.country_id', '=', auth()->user()->country);
        }

        return DataTables::of($merchants)
            ->addColumn('type', function ($row) {
                if ($row->type != null && isset(config('site.merchant_types')[$row->type]))
                    return config('site.merchant_types')[$row->type];
            })
            ->addColumn('username', function ($row) {
                $str = '';
                if ($row->type == 1) {
                    $str .= '<i class="fa fa-building"></i> ';
                    $str .= ucwords($row->name) . '<br>';
                } else if ($row->type == 2) {
                    $str .= '<i class="fa fa-building"></i> ';
                    $str .= ucwords($row->merchant_name) . '<br>';
                }
                $str .= '<i class="fa fa-user"></i> ';
                $str .= ucwords(strtolower($row->last_name . " " . $row->first_name));
                return $str;
            })
            ->addColumn('wallet', function ($row) {
                if ($row->wallet == null) {
                    return "0.00";
                }
                return number_format($row->wallet, 2);
            })
            ->addColumn('is_verified', function ($row) {
                return $row->is_verified == 1 ? 'Yes' : 'No';
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';

                if (auth()->user()->hasRole('super admin')) {
                    $html .= "<a href='" . url()->route('admin1.merchants1.edit', $row->id) . "' class='$iconClass'
                                data-toggle='tooltip' title='edit'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                    $html .= "<a title='Delete' href='javascript:;' data-id='$row->id' data-toggle='tooltip' 
                                class='$iconClass deleteMerchant'>
                                <i class='fa fa-trash'></i>
                            </a>";
                }

                $html .= "</div>";

                return $html;
            })
            ->rawColumns(['action', 'username'])
            ->make();
    }

    public function edit(Merchant $merchant)
    {
        $data = [];
        $data['merchant'] = $merchant;
        $country = session()->has('country') ? session()->get('country') : '';
        $data['countries'] = Country::pluckListing(auth()->user(), $country);
        $data['statuses'] = UserStatus::pluckListing([], 'merchant');
        $data['types'] = config('site.merchant_types');
        $data['merchants'] = Merchant::pluckListing(1, auth()->user());
        $data['lang'] = config('site.language');
        $data['emails'] = MerchantDetail::where('merchant_id', '=', $merchant->id)->where('type', '=', 1)->get();
        $data['commissions'] = MerchantCommission::where('merchant_id', '=', $merchant->id)->get();
        $data['branches'] = MerchantBranch::where('merchant_id', '=', $merchant->id)->get();
        $data['telephones'] = MerchantDetail::where('merchant_id', '=', $merchant->id)->where('type', '=', 2)->get();
        return view('admin1.pages.merchants.create', $data);
    }

    public function destroy(Merchant $merchant)
    {
        $data = [];
        $data['status'] = $merchant->delete();
        return $data;
    }

    public function branches(Merchant $merchant)
    {
        $data = [];
        if (request('type') == 'payment') {
//            Artisan::call('merchant:commission', ['id' => $merchant->id]);
        }
        $branches = MerchantBranch::where('merchant_id', $merchant->id)->pluck('name', 'id');
        $data['branches_amount'] = [];
        foreach ($branches as $id => $branch) {
            $data['branches_amount'][$id] = LoanTransaction::findRemainingAmount($merchant->id, $id, [], [], [1, 2]);
        }
        $data['branches'] = $branches;
        return $data;
    }

    public function resendVerificationMail(Merchant $merchant, MerchantDetail $id)
    {
        $data = [];

        Log::info('mail sent to ' . $id->sent_mail);

        $user_info = MerchantDetail::where('merchant_id', '=', $merchant->id)
            ->where('type', '=', 1)
            ->where('primary', '=', 1)
            ->first();

        $password = '';
        if ($user_info->is_verified == 0) {
            $password = $merchant->createPassword();
            $merchant->update([
                'password' => bcrypt($password),
            ]);
        }

        try {
            Mail::to($id->value)->send(new ConfirmMerchantEmail($merchant, $id->id, $id->value, $password));
        } catch (\Exception $e) {
            Log::error($e);
        }
        Log::info('verification mail sent to ' . $id->value . '.');

        return $data;
    }

    public function savePrimaryEmail(MerchantDetail $email_info)
    {
        $data = [];

        $merchant = Merchant::find($email_info->merchant_id);

        if ($email_info->value != request('value')) {
            $data['status'] = $email_info->update([
                'value'       => request('value'),
                'primary'     => 0,
                'is_verified' => 0,
            ]);
        }

        $data['infos'] = MerchantDetail::where('merchant_id', '=', $email_info->merchant_id)
            ->where('type', '=', 1)
            ->get();

        return $data;
    }

    public function payments()
    {
        return view('admin1.pages.merchants.payments');
    }

    public function reconciliations()
    {
        $data = [];
        $data['merchants'] = Merchant::pluckListing(1, auth()->user());
        return view('admin1.pages.merchants.reconciliations', $data);
    }

    public function reconciliationEdit(MerchantReconciliation $reconciliation)
    {
        $data = [];

        $data['inputs'] = [
            'id'          => ['type' => 'hidden', 'value' => $reconciliation->id],
            'merchant_id' => ['type' => 'select2', 'value' => $reconciliation->merchant_id],
            'branch_id'   => ['type' => 'select2', 'value' => $reconciliation->branch_id],
            'amount'      => ['type' => 'number', 'value' => $reconciliation->amount]
        ];
        $data['max'] = LoanTransaction::findRemainingAmount($reconciliation->merchant_id, $reconciliation->branch_id, [], [$reconciliation->id], [1, 2]);

        return $data;
    }

    public function reconciliationHistory(MerchantReconciliation $reconciliation)
    {
        $data = [];
        $history = MerchantReconciliationHistory::select('merchant_reconciliation_histories.*', 'users.firstname', 'users.lastname', 'merchants.name',
            'merchants.first_name', 'merchants.last_name', 'main.name as merchant_name', 'merchants.type as merchant_type')
            ->leftJoin('users', 'users.id', '=', 'merchant_reconciliation_histories.user_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'merchant_reconciliation_histories.user_id')
            ->leftJoin('merchants as main', 'main.id', '=', 'merchants.merchant_id')
            ->where('merchant_reconciliation_histories.merchant_reconciliation_id', '=', $reconciliation->id)
            ->orderBy('merchant_reconciliation_histories.id', 'asc')
            ->get();
        $history = $history->map(function ($item, $key) {
            if ($item->type == 'user') {
                $item->username = $item->firstname . ' ' . $item->lastname;
            } else {
                if ($item->merchant_type == 1) {
                    $item->username = $item->first_name . ' ' . $item->last_name . ' (' . $item->name . ')';
                } else {
                    $item->username = $item->first_name . ' ' . $item->last_name . ' (' . $item->merchant_name . ')';
                }
            }
            $item->date_time = Helper::date_time_to_current_timezone($item->created_at);
            $item->status = config('site.reconciliation_status')[$item->status];
            return $item;
        });
        $data['history'] = $history;
        return $data;
    }

    public function reconciliationStore()
    {
        $data = [];

        $reconciliation = MerchantReconciliation::find(request('id'));

        $this->validate(request(), MerchantReconciliation::validationRules(request()->all()), MerchantReconciliation::validationMessages());

        $types = [
            'merchant_id',
            'branch_id',
            'amount',
        ];

        $inputs = request()->only($types);

        if ($reconciliation != null) {
            $reconciliation->update($inputs);
        } else {
            $inputs['status'] = 1;
            $merchant = Merchant::find($inputs['merchant_id']);
            $country = Country::find($merchant->country_id);
            $inputs['date'] = Helper::time_to_current_timezone(date('Y-m-d H:i:s'), $country->timezone, 'Y-m-d');
            $reconciliation = MerchantReconciliation::create($inputs);
            $reconciliation->createTransactionId();
            MerchantReconciliationHistory::addStatusHistory($reconciliation->id, 1, 'user', auth()->user()->id);
        }
        $reconciliation->createOtp();

        $data['status'] = true;
        return $data;
    }

    public function reconciliationDatatable()
    {
        $data = MerchantReconciliation::select('merchant_reconciliations.*', 'merchants.name', 'merchant_branches.name as branch', 'merchants.country_id',
            'users.firstname', 'users.lastname')
            ->leftJoin('merchants', 'merchants.id', '=', 'merchant_reconciliations.merchant_id')
            ->leftJoin('merchant_branches', 'merchant_branches.id', '=', 'merchant_reconciliations.branch_id')
            ->leftJoin('users', 'users.id', '=', 'merchant_reconciliations.created_by');
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $data->where('merchants.country_id', '=', $country);
            }
        } else {
            $data->where('merchants.country_id', '=', auth()->user()->country);
        }
        return DataTables::of($data)
            ->addColumn('amount', function ($row) {
                return Helper::decimalShowing($row->amount, $row->country_id);
            })
            ->addColumn('created_by', function ($row) {
                return ucwords($row->firstname . ' ' . $row->lastname);
            })
            ->addColumn('status', function ($row) {
                if ($row->status != null) {
                    return config('site.reconciliation_status')[$row->status];
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                if ($row->status == 1) {
                    $html .= "<a href='javascript:;' title='Edit' data-toggle='tooltip' data-id='$row->id' class='$iconClass js--reconciliation-edit-button'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                    $html .= "<a href='javascript:;' title='Delete' data-toggle='tooltip' data-id='$row->id' class='$iconClass js--reconciliation-delete-button'><i class='fa fa-trash'></i></a>";
                    $html .= "<a href='javascript:;' title='View' data-toggle='tooltip' class='$iconClass js--reconciliation-edit-button' data-id='$row->id' data-type='view'>
                            <i class='fa fa-eye'></i>
                        </a>";
                }
                $html .= "<a href='javascript:;' title='History' data-toggle='tooltip' class='$iconClass js--reconciliation-history-button' data-id='$row->id'>
                            <i class='fa fa-history'></i>
                        </a>";
                $html .= '</div>';
                return $html;
            })
            ->make(true);
    }

    public function reconciliationDelete(MerchantReconciliation $reconciliation)
    {
        $data = [];

        $data['status'] = $reconciliation->delete();

        return $data;
    }

    public function generateQuery()
    {
        $transactions = LoanTransaction::select(DB::raw('sum(loan_transactions.amount) as collected_amount, sum(loan_transactions.commission_calculated) as commission, 
            month(convert_tz(loan_transactions.created_at,"+00:00",countries.time_offset)) as month, year(convert_tz(loan_transactions.created_at,"+00:00",countries.time_offset)) as year'),
            'merchants.type', 'merchants.first_name', 'merchants.last_name', 'main.name as merchant_name', 'merchants.name', 'merchants.country_id', 'merchant_branches.name as branch', 'countries.name as country',
            'loan_transactions.merchant_id', 'loan_transactions.branch_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'loan_transactions.merchant_id')
            ->leftJoin('countries', 'countries.id', '=', 'merchants.country_id')
            ->leftJoin('merchants as main', 'main.id', '=', 'merchants.merchant_id')
            ->leftJoin('merchant_branches', 'merchant_branches.id', '=', 'loan_transactions.branch_id');

        if (request('start_month')) {
            $date = Helper::currentTimezoneToUtcDateTime(Helper::frontToBackDate(request('start_month')) . ' 00:00:00');
            $transactions->where('loan_transactions.created_at', '>=', $date);
        }
        if (request('end_month')) {
            $date = Helper::currentTimezoneToUtcDateTime(Helper::frontToBackDate(request('end_month')) . ' 11:59:59');
            $transactions->where('loan_transactions.created_at', '<=', $date);
        }

        if (request('search_custom')) {
            $transactions->where(function ($query) {
                $query->where('countries.name', 'like', '%' . request('search_custom') . '%')
                    ->orWhere('merchant_branches.name', 'like', '%' . request('search_custom') . '%')
                    ->orWhere('merchants.name', 'like', '%' . request('search_custom') . '%')
                    ->orWhere('main.name', 'like', '%' . request('search_custom') . '%')
                    ->orWhere('merchants.first_name', 'like', '%' . request('search_custom') . '%')
                    ->orWhere('merchants.last_name', 'like', '%' . request('search_custom') . '%')
                    ->orWhere(DB::raw('concat(merchants.first_name," ",merchants.last_name)'), 'like', '%' . request('search_custom') . '%');
            });
        }

        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $transactions->where('merchants.country_id', '=', $country);
            }
        } else {
            $transactions->where('merchants.country_id', '=', auth()->user()->country);
        }

        $transactions->whereNotNull('loan_transactions.merchant_id');

        $transactions->groupBy(DB::raw('month(convert_tz(loan_transactions.created_at,"+00:00",countries.time_offset)), year(convert_tz(loan_transactions.created_at,"+00:00",countries.time_offset))'),
            'loan_transactions.merchant_id', 'loan_transactions.branch_id');

        return $transactions;
    }

    public function paymentsDatatable()
    {
        $payments = self::generateQuery();

        return DataTables::of($payments)
            ->addColumn('collected_amount', function ($row) {
                return Helper::decimalShowing($row->collected_amount, $row->country_id);
            })
            ->addColumn('commission', function ($row) {
                return Helper::decimalShowing($row->commission, $row->country_id);
            })
            ->addColumn('month', function ($row) {
                return $row->month . '/' . $row->year;
            })
            ->addColumn('created_by', function ($row) {
                if ($row->type == 1) {
                    return ucwords($row->name);
                } else {
                    return ucwords($row->first_name . ' ' . $row->last_name . ' (' . $row->merchant_name . ')');
                }
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';

                $html .= "<a href='#nogo' class='$iconClass js--transaction-show' data-toggle='tooltip' title='View' data-start='" . request('start_month') . "' data-end='" . request('end_month') . "' data-merchant='$row->merchant_id' data-branch='$row->branch_id'>
                            <i class='fa fa-eye'></i>
                        </a>";
                $html .= "</div>";

                return $html;
            })
            ->make(true);
    }

    public function exportPayment()
    {
        $payments = self::generateQuery();
        $payments = $payments->get();

        $data = [];

        foreach ($payments as $payment) {
            $merchant_name = '';
            if ($payment->type == 1) {
                $merchant_name = ucwords($payment->name);
            } else {
                $merchant_name = ucwords($payment->first_name . ' ' . $payment->last_name . ' (' . $payment->merchant_name . ')');
            }
            $element = [
                'Country'          => $payment->country,
                'Merchant'         => $merchant_name,
                'Branch'           => $payment->branch,
                'Month'            => $payment->month . '/' . $payment->year,
                'Collected Amount' => Helper::decimalShowing($payment->collected_amount, $payment->country_id),
                'Commission'       => Helper::decimalShowing($payment->commission, $payment->country_id)
            ];
            $data[] = $element;
        }

        $filename = time() . auth()->user()->id . '-payments-excel';

        Excel::create($filename, function ($excel) use ($filename, $data) {
            $excel->setTitle($filename);
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx', public_path() . '/uploads/excel');

        $data = [];
        $data['url'] = asset('uploads/excel/' . $filename . '.xlsx');
        return $data;
    }

    public function merchantTransactionsDatatable()
    {
        $transactions = LoanTransaction::select('loan_transactions.*', 'users.firstname', 'users.lastname', 'users.id_number', 'merchants.first_name', 'merchants.last_name',
            'merchants.type as merchant_type', 'merchants.name as merchant', 'main.name as merchant_name')
            ->leftJoin('users', 'users.id', '=', 'loan_transactions.client_id')
            ->leftJoin('merchants', 'merchants.id', '=', 'loan_transactions.created_by')
            ->leftJoin('merchants as main', 'main.id', '=', 'merchants.merchant_id');

        if (request('merchant_id')) {
            $transactions->where('loan_transactions.merchant_id', '=', request('merchant_id'));
        }
        if (request('branch_id')) {
            $transactions->where('loan_transactions.branch_id', '=', request('branch_id'));
        }
        if (request('start_date')) {
            $date = Helper::currentTimezoneToUtcDateTime(Helper::frontToBackDate(request('start_date')) . ' 00:00:00');
            $transactions->where('loan_transactions.created_at', '>=', $date);
        }
        if (request('merchant')) {
            $date = Helper::currentTimezoneToUtcDateTime(Helper::frontToBackDate(request('end_date')) . ' 11:59:59');
            $transactions->where('loan_transactions.created_at', '<=', $date);
        }

        return DataTables::of($transactions)
            ->addColumn('loan_id', function ($row) {
                return '<a href="' . route('admin1.loans.calculation-history', $row->loan_id) . '" target="_blank">' . $row->loan_id . '</a>';
            })
            ->addColumn('created_at', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->addColumn('client', function ($row) {
                return $row->firstname . ' ' . $row->lastname;
            })
            ->addColumn('received_by', function ($row) {
                if ($row->merchant_type == 1) {
                    return $row->first_name . ' ' . $row->last_name . ' (' . $row->merchant . ')';
                } else {
                    return $row->first_name . ' ' . $row->last_name . ' (' . $row->merchant_name . ')';
                }
            })
            ->rawColumns(['loan_id'])
            ->make(true);
    }
}