<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Credit;
use App\Models\LoanTransaction;
use Collective\Html\FormFacade;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BankReconcileController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin')->except('audit');
        $this->middleware('role:super admin|auditor')->only('audit');
    }

    public function index()
    {
        return view('admin1.pages.bank.reconcile');
    }

    public function indexDatatable()
    {
        $transaction_selection = [
            'loan_transactions.id',
            'loans_users.firstname',
            'loans_users.lastname',
            'loans_users.country',
            DB::raw('(loan_transactions.amount-loan_transactions.cash_back_amount) as amount'),
            'loan_transactions.loan_id',
            DB::raw('date(loan_transactions.payment_date) as date'),
            'loan_transactions.reconciled_at',
        ];
        $loan_transactions = LoanTransaction::select($transaction_selection)
            ->leftJoin('users as loans_users', 'loans_users.id', '=', 'loan_transactions.client_id')
            ->where('loan_transactions.payment_type', '=', 5);
        if (request('reconcile_type') == 1) {
        } else if (request('reconcile_type') == 2) {
            $loan_transactions->whereNotNull('loan_transactions.reconciled_at');
        } else if (request('reconcile_type') == 3) {
            $loan_transactions->whereNull('loan_transactions.reconciled_at');
        }

        if (session()->has('country')) {
            $loan_transactions->where('loans_users.country', '=', session('country'));
        }

        if (request('search')['value'] != '') {
            $loan_transactions->where(function ($query) {
                $query->where('loans_users.firstname', 'like', '%' . request('search')['value'] . '%')
                    ->orwhere('loans_users.lastname', 'like', '%' . request('search')['value'] . '%')
                    ->orwhere(DB::raw('concat(loans_users.firstname," ",loans_users.lastname)'), 'like', '%' . request('search')['value'] . '%');
            });
        }

        $credit_selection = [
            'credits.id',
            'users.firstname',
            'users.lastname',
            'users.country',
            'credits.amount',
            DB::raw('"" as loan_id'),
            DB::raw('date(credits.created_at) as date'),
            'credits.reconciled_at',
        ];
        $credits = Credit::select($credit_selection)
            ->leftJoin('users', 'users.id', '=', 'credits.user_id')
            ->where('credits.payment_type', '=', 2)
            ->where('credits.status', '=', 3);

        if (session()->has('country')) {
            $credits->where('users.country', '=', session('country'));
        }

        if (request('search')['value'] != '') {
            $credits->where(function ($query) {
                $query->where('users.firstname', 'like', '%' . request('search')['value'] . '%')
                    ->orwhere('users.lastname', 'like', '%' . request('search')['value'] . '%')
                    ->orwhere(DB::raw('concat(users.firstname," ",users.lastname)'), 'like', '%' . request('search')['value'] . '%');
            });
        }

        $credits->union($loan_transactions);

        if (request('reconcile_type') == 1) {
        } else if (request('reconcile_type') == 2) {
            $credits->whereNotNull('credits.reconciled_at');
        } else if (request('reconcile_type') == 3) {
            $credits->whereNull('credits.reconciled_at');
        }

        $inputs = request()->all();
        if (isset($inputs['order']) && isset($inputs['order'][0]) && isset($inputs['order'][0]['column'])) {
            if ($inputs['order'][0]['column'] == 1) {
                $credits->orderBy('date', $inputs['order'][0]['dir']);
            }
        }

        return DataTables::of($credits)
            ->addColumn('amount', function ($row) {
                return Helper::decimalShowing($row->amount, $row->country);
            })
            ->addColumn('reconcile_select', function ($data) {
                if ($data->reconciled_at == null) {
                    if ($data->loan_id == null) {
                        $type = 'credit';
                    } else {
                        $type = 'loan';
                    }
                    return '<label style="display:flex; height:28px;margin: 0;align-items: center; justify-content: center;">' . FormFacade::checkbox('reconcile_select', $data->id, false, [
                            'id'        => 'checkbox_' . $data->id . '_' . $type,
                            'class'     => 'reconcileCheckbox',
                            'data-type' => $type
                        ]) . '</label>';
                }
            })
            ->setRowAttr([
                'data-id'   => function ($data) {
                    return $data->id;
                },
                'data-type' => function ($data) {
                    if ($data->loan_id == null) {
                        return 'credit';
                    } else {
                        return 'loan_transactions';
                    }
                }
            ])
            ->addColumn('type', function ($data) {
                if ($data->loan_id == null) {
                    return 'Debit';
                } else {
                    return 'Credit';
                }
            })
            ->addColumn('date', function ($data) {
                if ($data->date != null) {
                    return Helper::datebaseToFrontDate($data->date);
                }
            })
            ->addColumn('status', function ($data) {
                if ($data->reconciled_at == null) {
                    return 'Non-reconciled';
                } else {
                    return 'Reconciled';
                }
            })
            ->addColumn('action', function ($data) {
                if ($data->reconciled_at == null) {
                    if ($data->loan_id == null) {
                        $type = 'credit';
                    } else {
                        $type = 'loan';
                    }
                    return "<a href='javascript:;' data-toggle='tooltip' title='Reconcile' data-id='$data->id' data-type='$type' class='btn btn-icon btn-sm btn-success waves-effect reconcileBank'>
                                <i class='fa fa-check'></i>
                            </a>";
                }
            })
            ->addColumn('fullname', function ($data) {
                return $data->firstname . ' ' . $data->lastname;
            })
            ->rawColumns(['action', 'reconcile_select'])
            ->make(true);
    }

    public function reconcile()
    {
        $data = [];
        $ids = explode(',', request('id'));
        $types = explode(',', request('type'));
        foreach ($ids as $key => $value) {
            if ($types[$key] == 'credit') {
                Credit::where('id', '=', $value)
                    ->where('payment_type', '=', 2)
                    ->where('credits.status', '=', 3)
                    ->update([
                        'reconciled_at' => date('Y-m-d H:i:s')
                    ]);
            } else if ($types[$key] == 'loan') {
                LoanTransaction::where('id', '=', $value)
                    ->where('payment_type', '=', 5)
                    ->update([
                        'reconciled_at' => date('Y-m-d H:i:s')
                    ]);
            }
        }
        $data['status'] = true;
        return $data;
    }

    public function audit()
    {
        $data = [];
        $years = [];
        for ($i = 2018; $i <= date('Y'); $i++) {
            $years[$i] = $i;
        }
        $data['years'] = $years;
        return view('admin1.pages.bank.audit', $data);
    }

    public function auditData()
    {
        $data = [];
        $year = request('year');
        $credits = LoanTransaction::select(DB::raw('sum(amount - cash_back_amount) as amount'), DB::raw('month(payment_date)'))
            ->whereYear('payment_date', '=', $year)
            ->where('payment_type', '=', 5)
            ->groupBy(DB::raw('month(payment_date)'))
            ->pluck('amount', DB::raw('month(payment_date)'));
        $re_credits = LoanTransaction::select(DB::raw('sum(amount - cash_back_amount) as amount'), DB::raw('month(payment_date)'))
            ->whereYear('payment_date', '=', $year)
            ->where('payment_type', '=', 5)
            ->whereNotNull('reconciled_at')
            ->groupBy(DB::raw('month(payment_date)'))
            ->pluck('amount', DB::raw('month(payment_date)'));
        $debits = Credit::select(DB::raw('sum(amount) as amount'), DB::raw('month(created_at)'))
            ->whereYear('created_at', '=', $year)
            ->where('payment_type', '=', 2)
            ->where('status', '=', 3)
            ->groupBy(DB::raw('month(created_at)'))
            ->pluck('amount', DB::raw('month(created_at)'));
        $re_debits = Credit::select(DB::raw('sum(amount) as amount'), DB::raw('month(created_at)'))
            ->whereYear('created_at', '=', $year)
            ->where('payment_type', '=', 2)
            ->where('status', '=', 3)
            ->whereNotNull('reconciled_at')
            ->groupBy(DB::raw('month(created_at)'))
            ->pluck('amount', DB::raw('month(created_at)'));
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July ',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
        foreach ($months as $key => $value) {
            $credit = 0;
            $re_credit = 0;
            $debit = 0;
            $re_debit = 0;
            $diff = 0;
            $re_diff = 0;
            if (isset($credits[$key + 1])) {
                $credit = $credits[$key + 1];
            }
            if (isset($re_credits[$key + 1])) {
                $re_credit = $re_credits[$key + 1];
            }
            if (isset($debits[$key + 1])) {
                $debit = $debits[$key + 1];
            }
            if (isset($re_debits[$key + 1])) {
                $re_debit = $re_debits[$key + 1];
            }
            $diff = $credit - $re_credit;
            $re_diff = $debit - $re_debit;
            $data[] = [
                'month'         => $value,
                'credit'        => Helper::decimalShowing($credit, auth()->user()->country),
                're_credit'     => Helper::decimalShowing($re_credit, auth()->user()->country),
                'debit'         => Helper::decimalShowing($debit, auth()->user()->country),
                're_debit'      => Helper::decimalShowing($re_debit, auth()->user()->country),
                'difference'    => Helper::decimalShowing($diff, auth()->user()->country),
                're_difference' => Helper::decimalShowing($re_diff, auth()->user()->country),
            ];
        }
        return $data;
    }
}
