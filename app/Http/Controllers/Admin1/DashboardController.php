<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Credit;
use App\Models\Dayopen;
use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanStatus;
use App\Models\LoanStatusHistory;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:processor')->only([
            'branch',
            'branchStore',
        ]);
    }

    public function index()
    {
        $data = [];
        $data['countries'] = Country::pluck('name', 'id');

        $data['branches'] = Branch::select('*');
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
        } else {
            $country = auth()->user()->country;
        }
        if ($country != '') {
            $data['branches']->where('country_id', '=', $country);
        }
        $data['branches_history'] = [0 => 'All'] + $data['branches']->orderBy('title', 'asc')->pluck('title', 'id')->toArray();
        $data['branches'] = $data['branches']->orderBy('title', 'asc')->pluck('title', 'id');
        $data['transactions'] = [0 => 'All'] + config('site.payment_types');
        $data['credits'] = [0 => 'All'] + ['1' => 'Wallet Credit', '2' => 'Wallet Withdrawal'];
        $users = User::whereIn('role_id', [1, 2, 5, 6, 9])
            ->select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'id');
        if ($country != '') {
            $users->where('country', '=', $country);
        }
        $users = $users->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();
        $data['users'] = [0 => 'All'] + $users;
        $clients = User::whereIn('role_id', [3])
            ->select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'id');
        if ($country != '') {
            $clients->where('country', '=', $country);
        }
        $clients = $clients->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();
        $data['clients'] = [0 => 'All'] + $clients;
        return view('admin1.pages.dashboard.index', $data);
    }

    public function getData()
    {
        $country = null;

        if (request('country') && auth()->user()->hasRole('super admin')) {
            $country = request('country');
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $country = auth()->user()->country;
            }

            if (auth()->user()->hasRole('super admin') && session()->has('country')) {
                $country = session('country');
            }
        }

        //        $start_date = date('Y-m-d', strtotime('first day of this month'));
        $end_date = date('Y-m-d');
        $format = config('site.date_format.php');
        //        if (request('start_date')) {
        //            $date = \DateTime::createFromFormat($format, request('start_date'));
        //            $start_date = $date->format('Y-m-d');
        //        }
        if (request('end_date')) {
            $date = \DateTime::createFromFormat($format, request('end_date'));
            $end_date = $date->format('Y-m-d');
        }
        $loan_status_id = [1, 3, 4, 5, 6];

        if (auth()->user()->hasRole('debt collector')) {
            $loan_status_id = [6];
        }

        $loan_statuses = LoanStatus::whereIn('id', $loan_status_id)->get();

        $loan_ids = LoanStatusHistory::select(DB::raw('max(id)'), 'loan_id')->groupBy('loan_id')->pluck('max(id)');

        $loans = LoanStatusHistory::select('loan_status_histories.*')
            ->leftJoin('loan_applications', 'loan_applications.id', '=', 'loan_status_histories.loan_id')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            //            ->whereDate('loan_status_histories.created_at', '>=', $start_date)
            ->whereDate('loan_status_histories.created_at', '<=', $end_date)
            ->whereNull('loan_applications.deleted_at')
            ->whereIn('loan_status_histories.id', $loan_ids)
            ->where('users.web_registered', '=', 1)
            ->whereNull('users.deleted_at');
        if ($country != null) {
            $loans->where('users.country', '=', $country);
        }
        $loans = $loans->groupBy('loan_status_histories.loan_id', 'loan_status_histories.id')
            ->get();


        $loan_calculation_histories = LoanCalculationHistory::select(DB::raw('max(id)'), 'loan_id')
            ->whereIn('loan_id', $loans->pluck('loan_id'))
            ->whereDate('created_at', '<=', $end_date)
            ->groupBy('loan_id')
            ->pluck('max(id)');

        $loan_calculation_histories = LoanCalculationHistory::whereIn('loan_id', $loans->pluck('loan_id'))
            //            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereIn('id', $loan_calculation_histories)
            ->get()
            ->keyBy('loan_id');

        $loan_transactions = LoanTransaction::select(DB::raw('sum(amount) as amount'), DB::raw('sum(cash_back_amount) as cash_back_amount'), 'loan_id')
            ->whereIn('loan_id', $loans->pluck('loan_id'))
            ->where('payment_date', '<=', $end_date)
            ->groupBy('loan_id')
            ->get()
            ->keyBy('loan_id');

        foreach ($loans as $key => $item) {
            $sum = 0;
            $cashback_amount = 0;
            if (isset($loan_transactions[$item->loan_id])) {
                $sum = $loan_transactions[$item->loan_id]['amount'];
                $cashback_amount = $loan_transactions[$item->loan_id]['cash_back_amount'];
            }

            $item->after_debt = $sum - $cashback_amount;

            $entry = null;
            if (isset($loan_calculation_histories[$item->loan_id])) {
                $entry = $loan_calculation_histories[$item->loan_id];
            }

            $item->principal = 0;
            $item->total = 0;
            if ($entry != null) {
                $item->principal = $entry->principal;
                $item->total = $entry->total;
            }
        }

        $rows = [];

        foreach ($loan_statuses as $value) {
            $rows[$value->title] = [
                'Status'      => $value->title,
                'Loans_value' => $loans->where('status_id', '=', $value->id)->count(),
                'Loans'       => Helper::numberShowing($loans->where('status_id', '=', $value->id)->count()),
            ];
            if (auth()->user()->hasRole('super admin|admin')) {
                $rows[$value->title] += [
                    'Outstanding_value'      => $loans->where('status_id', '=', $value->id)->sum('principal'),
                    'Outstanding'            => Helper::decimalShowing($loans->where('status_id', '=', $value->id)->sum('principal'), $country),
                    'Payment Received_value' => $loans->where('status_id', '=', $value->id)->sum('after_debt'),
                    'Payment Received'       => Helper::decimalShowing($loans->where('status_id', '=', $value->id)->sum('after_debt'), $country),
                ];
            } else if (auth()->user()->hasRole('debt collector')) {
                $rows[$value->title] += [
                    'Outstanding_value'      => $loans->where('status_id', '=', $value->id)->sum('total'),
                    'Outstanding'            => Helper::decimalShowing($loans->where('status_id', '=', $value->id)->sum('total'), $country),
                    'Payment Received_value' => $loans->where('status_id', '=', $value->id)->sum('after_debt'),
                    'Payment Received'       => Helper::decimalShowing($loans->where('status_id', '=', $value->id)->sum('after_debt'), $country),
                ];
            }
        }
        $cal = collect($rows);

        $rows['Total'] = [
            'Status' => 'Total',
            'Loans'  => Helper::numberShowing($cal->sum('Loans_value')),
        ];
        if (auth()->user()->hasRole('super admin|admin|debt collector')) {
            $rows['Total'] += [
                'Outstanding'      => Helper::decimalShowing($cal->sum('Outstanding_value'), $country),
                'Payment Received' => Helper::decimalShowing($cal->sum('Payment Received_value'), $country),
            ];
        }
        $users = User::select('users.*')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.role_id', '=', 3);
        if ($country != null) {
            $users->where('users.country', '=', $country);
        }
        $users = $users->get();

        $wallets = Wallet::whereIn('user_id', $users->pluck('id'))->sum('amount');
        $data = [];
        $data['rows'] = $rows;
        $data['total_clients'] = $users->count();
        $data['total_credits'] = Helper::decimalShowing($wallets, $country);
        return $data;
    }

    public function branch()
    {
        $data = [];
        $data['branches'] = auth()->user()->userBranches->pluck('title', 'id');
        return view('admin1.pages.branch-select', $data);
    }

    public function branchStore()
    {
        $this->validate(request(), ['branch_id' => 'required'], ['branch_id.required' => 'The branch field selection is required']);

        session(['branch_id' => request('branch_id')]);
        $branch = Branch::find(request('branch_id'));
        session(['branch_name' => $branch->title]);
        return redirect()->route('admin1.home');
    }

    public function permissionDenied()
    {
        return view('admin1.pages.permission-denied');
    }

    public function totalData()
    {
        $data = [];
        $country = null;

        if (request('country') && auth()->user()->hasRole('super admin')) {
            $country = request('country');
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $country = auth()->user()->country;
            }

            if (auth()->user()->hasRole('super admin') && session()->has('country')) {
                $country = session('country');
            }
        }

        $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 11:59:59';
        $format = config('site.date_format.php');
        if (request('start_date')) {
            $date = \DateTime::createFromFormat($format, request('start_date'));
            $start_date = $date->format('Y-m-d') . ' 00:00:00';
        }
        if (request('end_date')) {
            $date = \DateTime::createFromFormat($format, request('end_date'));
            $end_date = $date->format('Y-m-d') . ' 23:59:59';
        }

        $start_date = Helper::currentTimezoneToUtcDateTime($start_date);
        $end_date = Helper::currentTimezoneToUtcDateTime($end_date);

        info($start_date);
        info($end_date);

        $statuses = [3, 4, 5, 6, 7, 8, 9, 10];

        $main_loans_counts = LoanApplication::select(DB::raw('count(loan_applications.id) as total_loans, sum(loan_applications.amount) as total_amount'))
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->where('loan_applications.start_date', '>=', $start_date)
            ->where('loan_applications.start_date', '<=', $end_date)
            ->where('users.web_registered', '=', 1)
            ->whereNull('users.deleted_at');
        if ($country != null) {
            $main_loans_counts->where('users.country', '=', $country);
        }
        $main_loans_counts = $main_loans_counts->first();

        $loans = LoanStatusHistory::select('loan_status_histories.*')
            ->leftJoin('loan_applications', 'loan_applications.id', '=', 'loan_status_histories.loan_id')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->whereIn('loan_status_histories.status_id', $statuses)
            ->where('users.web_registered', '=', 1)
            ->whereDate('loan_status_histories.created_at', '>=', $start_date)
            ->whereDate('loan_status_histories.created_at', '<=', $end_date)
            ->whereNull('loan_applications.deleted_at')
            ->whereNull('users.deleted_at');
        if ($country != null) {
            $loans->where('users.country', '=', $country);
        }
        $loans = $loans->groupBy('loan_status_histories.loan_id', 'loan_status_histories.id')
            ->pluck('loan_status_histories.loan_id');

        $loans_counts = LoanApplication::select(DB::raw('count(id) as total_loans, sum(amount) as total_amount, sum(origination_fee) as total_origination_amount'))
            ->whereIn('id', $loans)
            ->first();


        $c_loans = LoanStatusHistory::select('loan_status_histories.*')
            ->leftJoin('loan_applications', 'loan_applications.id', '=', 'loan_status_histories.loan_id')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->whereIn('loan_status_histories.status_id', $statuses)
            ->where('users.web_registered', '=', 1)
            ->whereNull('loan_applications.deleted_at')
            ->whereNull('users.deleted_at');
        if ($country != null) {
            $c_loans->where('users.country', '=', $country);
        }
        $c_loans = $c_loans->groupBy('loan_status_histories.loan_id', 'loan_status_histories.id')
            ->pluck('loan_status_histories.loan_id');

        $loan_calculation_histories = LoanCalculationHistory::select(DB::raw('max(id) as main_ids'), 'loan_id')
            ->whereIn('loan_id', $c_loans)
            ->whereYear('created_at', '=', date('Y'))
            ->groupBy('loan_id')
            ->get()
            ->pluck('main_ids');

        //        Log::info($loan_calculation_histories);

        $posted_loan_calculation_histories = LoanCalculationHistory::whereIn('loan_id', $loans)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereNull('payment_amount')
            ->pluck('id');

        $collected_loan_calculation_histories = LoanCalculationHistory::whereIn('loan_id', $loans)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereNotNull('payment_amount')
            ->pluck('id');

        $collected_principal = LoanCalculationHistory::whereIn('id', $collected_loan_calculation_histories)->sum('principal_posted');
        $renewal_fees = LoanCalculationHistory::whereIn('id', $posted_loan_calculation_histories)->sum('renewal_posted');
        $admin_fees = LoanCalculationHistory::whereIn('id', $posted_loan_calculation_histories)->sum('debt_posted');
        $interest_fees = LoanCalculationHistory::whereIn('id', $posted_loan_calculation_histories)->sum('interest_posted');
        $origination_fees = LoanCalculationHistory::whereIn('id', $posted_loan_calculation_histories)->sum('origination_posted');

        $debt_loan_calculation_histories = LoanCalculationHistory::whereIn('loan_id', $loans)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereNotNull('debt_collection_value')
            ->where('debt_collection_value', '>', 0)
            ->pluck('id');
        $debt_collection_fees = LoanCalculationHistory::whereIn('id', $debt_loan_calculation_histories)->sum('debt_collection_value_posted');
        //        \Log::info($loans);
        $payment_loan_calculation_histories = LoanCalculationHistory::whereIn('loan_id', $loans)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereNotNull('payment_amount')
            ->pluck('id');

        $debt_c_fees_collected = LoanCalculationHistory::whereIn('id', $payment_loan_calculation_histories)->sum('debt_collection_value_posted');
        $renewal_fees_collected = LoanCalculationHistory::whereIn('id', $payment_loan_calculation_histories)->sum('renewal_posted');
        $admin_fees_collected = LoanCalculationHistory::whereIn('id', $payment_loan_calculation_histories)->sum('debt_posted');
        $interest_collected = LoanCalculationHistory::whereIn('id', $payment_loan_calculation_histories)->sum('interest_posted');
        $total_collected = round(($origination_fees > 0 ? $origination_fees : 0) + $debt_c_fees_collected + $renewal_fees_collected + $admin_fees_collected + $interest_collected,
            2);


        $loan_calculation_histories = LoanCalculationHistory::select(DB::raw('sum(principal) as principal_outstanding'), DB::raw('sum(renewal) as renewal_outstanding'),
            DB::raw('sum(debt_collection_value) as debt_outstanding'), DB::raw('sum(debt) as admin_outstanding'), DB::raw('sum(interest) as interest_outstanding'))
            ->whereIn('id', $loan_calculation_histories)
            ->first();

        $data = [
            'Total Loans Count'     => $main_loans_counts->total_loans > 0 ? Helper::numberShowing($main_loans_counts->total_loans) : 0,
            'Total Loans Amount'    => $main_loans_counts->total_amount > 0 ? Helper::decimalShowing($main_loans_counts->total_amount, $country) : 0,
            'Total Loans Collected' => $collected_principal > 0 ? Helper::decimalShowing($collected_principal, $country) : 0,


            'Total Renewal Fees Posted'         => $renewal_fees > 0 ? Helper::decimalShowing($renewal_fees, $country) : 0,
            'Total Debt Collection Fees Posted' => $debt_collection_fees > 0 ? Helper::decimalShowing($debt_collection_fees, $country) : 0,
            'Total Admin Fees Posted'           => $admin_fees > 0 ? Helper::decimalShowing($admin_fees, $country) : 0,
            'Total Interest Posted'             => $interest_fees > 0 ? Helper::decimalShowing($interest_fees, $country) : 0,
            'Total Origination fee'             => $origination_fees > 0 ? Helper::decimalShowing($origination_fees, $country) : 0,

            'Total Renewal Fees Collected'         => $renewal_fees_collected > 0 ? Helper::decimalShowing($renewal_fees_collected, $country) : 0,
            'Total Debt Collection Fees Collected' => $debt_c_fees_collected > 0 ? Helper::decimalShowing($debt_c_fees_collected, $country) : 0,
            'Total Admin Fees Collected'           => $admin_fees_collected > 0 ? Helper::decimalShowing($admin_fees_collected, $country) : 0,
            'Total Interest Collected'             => $interest_collected > 0 ? Helper::decimalShowing($interest_collected, $country) : 0,
            'Total Collected'                      => $total_collected > 0 ? Helper::decimalShowing($total_collected, $country) : 0,

            'Total Principal Outstanding'           => $loan_calculation_histories->principal_outstanding > 0 ? Helper::decimalShowing($loan_calculation_histories->principal_outstanding,
                $country) : 0,
            'Total Renewal Fees Outstanding'        => $loan_calculation_histories->renewal_outstanding > 0 ? Helper::decimalShowing($loan_calculation_histories->renewal_outstanding,
                $country) : 0,
            'Total Debt Collector Fees Outstanding' => $loan_calculation_histories->debt_outstanding > 0 ? Helper::decimalShowing($loan_calculation_histories->debt_outstanding,
                $country) : 0,
            'Total Interest Outstanding'            => $loan_calculation_histories->interest_outstanding > 0 ? Helper::decimalShowing($loan_calculation_histories->interest_outstanding,
                $country) : 0,
            'Total Admin Fees Outstanding'          => $loan_calculation_histories->admin_outstanding > 0 ? Helper::decimalShowing($loan_calculation_histories->admin_outstanding,
                $country) : 0,
        ];

        return $data;
    }

    public function totalExcel()
    {
        $data = [];
        $data_above = self::getData();
        $data_below = self::totalData();

        $country = null;

        if (request('country') && auth()->user()->hasRole('super admin')) {
            $country = request('country');
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $country = auth()->user()->country;
            }

            if (auth()->user()->hasRole('super admin') && session()->has('country')) {
                $country = session('country');
            }
        }

        if ($country != null && $country != '' && $country != '0') {
            $country = Country::find($country);
            if ($country != null) {
                $country = strtolower($country->name);
            }
        } else {
            $country = 'all';
        }

        $start_date = date('Ymd', strtotime('first day of this month'));
        $end_date = date('Ymd');
        $format = config('site.date_format.php');
        if (request('start_date')) {
            $date = \DateTime::createFromFormat($format, request('start_date'));
            $start_date = $date->format('dmY');
        }
        if (request('end_date')) {

            $date = \DateTime::createFromFormat($format, request('end_date'));
            $end_date = $date->format('dmY');
        }

        $filename = $start_date . '_' . $end_date . '_' . $country;

        Excel::create($filename, function ($excel) use ($filename, $data_above, $data_below) {
            $excel->setTitle($filename);
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data_above, $data_below) {
                //                $sheet->row(1, ['Total clients', $data_above['total_clients']]);
                //                $sheet->row(2, ['Total credit in wallet', $data_above['total_credits']]);
                //                $sheet->fromArray($data_above['rows'], null, 'A4');

                $index = 1;
                foreach ($data_below as $key => $value) {
                    $sheet->row($index, [$key, $value]);
                    $index++;
                }
            });
        })->store('xlsx', public_path() . '/uploads/excel');

        $data['url'] = asset('uploads/excel/' . $filename . '.xlsx');

        return $data;
    }

    public function historyExcel()
    {
        $data = [];
        $country = null;

        if (request('country') && auth()->user()->hasRole('super admin')) {
            $country = request('country');
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $country = auth()->user()->country;
            }

            if (auth()->user()->hasRole('super admin') && session()->has('country')) {
                $country = session('country');
            }
        }

        if ($country != null && $country != '' && $country != '0') {
            $country = Country::find($country);
            if ($country != null) {
                $country = strtolower($country->name);
            }
        } else {
            $country = 'all';
        }

        $start_date = date('Ymd', strtotime('first day of this month'));
        $end_date = date('Ymd');
        $format = config('site.date_format.php');
        if (request('start_date')) {
            $date = \DateTime::createFromFormat($format, request('start_date'));
            $start_format_date = $date->format('Y-m-d');
            $start_date = $date->format('dmY');
        }
        if (request('end_date')) {

            $date = \DateTime::createFromFormat($format, request('end_date'));
            $end_format_date = $date->format('Y-m-d');
            $end_date = $date->format('dmY');
        }

        $filename = $start_date . '_' . $end_date . '_' . $country;

        $selections = [
            'loan_calculation_histories.*',
            DB::raw('concat(users.firstname," ",users.lastname) as user'),
            'branches.title as branch',
            DB::raw('concat(employees.firstname," ",employees.lastname) as employee'),
        ];
        $histories = LoanCalculationHistory::select($selections)
            ->leftJoin('loan_applications', 'loan_applications.id', '=', 'loan_calculation_histories.loan_id')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->leftJoin('users as employees', 'employees.id', '=', 'loan_calculation_histories.employee_id')
            ->leftJoin('loan_transactions', 'loan_transactions.used', '=', 'loan_calculation_histories.id')
            ->leftJoin('branches', 'branches.id', '=', 'loan_transactions.branch_id');
        if (request('branch')) {
            $histories->where('loan_transactions.branch_id', '=', request('branch'));
        }
        if (request('client')) {
            $histories->where('loan_applications.client_id', '=', request('client'));
        }
        if (request('user')) {
            $histories->where('loan_applications.employee_id', '=', request('user'));
        }
        $histories = $histories->whereDate('loan_calculation_histories.created_at', '>=', $start_format_date)
            ->whereDate('loan_calculation_histories.created_at', '<=', $end_format_date)
            ->groupBy('loan_calculation_histories.id')
            ->get();

        $data = [];

        foreach ($histories as $history) {
            $element = [
                'Loan ID'               => $history->loan_id,
                'Client name'           => $history->user,
                'Branch'                => $history->branch,
                'User'                  => $history->employee,
                'Iteration'             => $history->week_iterations,
                'Date'                  => Helper::date_to_current_timezone($history->created_at),
                'Payment Amount'        => $history->payment_amount,
                'Transaction Name'      => $history->transaction_name,
                'Commission (%)'        => $history->commission_percent,
                'Commission'            => $history->commission,
                'Principal'             => $history->principal,
                'Origination'           => $history->origination,
                'Interest'              => $history->interest,
                'Renewal'               => $history->renewal,
                'Tax'                   => $history->tax,
                'Tax for origination'   => $history->tax_for_origination,
                'Tax for renewal'       => $history->tax_for_renewal,
                'Tax for interest'      => $history->tax_for_interest,
                'Debt'                  => $history->debt,
                'Debt tax'              => $history->debt_tax,
                'Debt collection value' => $history->debt_collection_value,
                'Debt collection tax'   => $history->debt_collection_tax,
                'Total tax'             => $history->total_e_tax,
                'Total Balance'         => $history->total,
            ];
            $data[] = $element;
        }

        Excel::create($filename, function ($excel) use ($filename, $data) {
            $excel->setTitle($filename);
            //Chain the setters
            $excel->sheet('Loan History', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx', public_path() . '/uploads/excel');

        $data = [];
        $data['url'] = asset('uploads/excel/' . $filename . '.xlsx');

        return $data;
    }

    public function cashDataPdf()
    {
        $country = null;

        if (request('country') && auth()->user()->hasRole('super admin')) {
            $country = request('country');
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $country = auth()->user()->country;
            }

            if (auth()->user()->hasRole('super admin') && session()->has('country')) {
                $country = session('country');
            }
        }
        $country_id = $country;

        if ($country != null && $country != '' && $country != '0') {
            $country = Country::find($country);
            if ($country != null) {
                $country = strtolower($country->name);
            }
        } else {
            $country = 'all';
        }

        $branch_id = request('branch');

        $start_date = date('Y-m-d', strtotime('first day of this month')) . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 11:59:59';
        $format = config('site.date_format.php');
        if (request('start_date')) {
            $date = \DateTime::createFromFormat($format, request('start_date'));
            $start_date = $date->format('Y-m-d') . ' 00:00:00';
        }
        if (request('end_date')) {
            $date = \DateTime::createFromFormat($format, request('end_date'));
            $end_date = $date->format('Y-m-d') . ' 11:59:59';
        }

        $main_start_date = date('Y-m-d', strtotime($start_date));
        $main_end_date = date('Y-m-d', strtotime($end_date));

        $start_date = Helper::currentTimezoneToUtcDateTime($start_date);
        $end_date = Helper::currentTimezoneToUtcDateTime($end_date);

        $transaction_names = ['Payment'];

        $iterations = LoanCalculationHistory::select('loan_calculation_histories.*', DB::raw('concat(users.firstname," ",users.lastname) as username'),
            DB::raw('concat(transaction_user.firstname," ",transaction_user.lastname) as transaction_username'))
            ->join('loan_applications', 'loan_applications.id', '=', 'loan_calculation_histories.loan_id')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
            ->leftJoin('loan_transactions', 'loan_transactions.used', '=', 'loan_calculation_histories.id')
            ->leftJoin('users as transaction_user', 'transaction_user.id', '=', 'loan_transactions.created_by')
            ->where('loan_calculation_histories.created_at', '>=', $start_date)
            ->where('loan_calculation_histories.created_at', '<=', $end_date)
            ->whereIn('transaction_name', $transaction_names);
        if ($country_id != null) {
            $iterations->where('users.country', '=', $country_id);
        }
        $iterations->where('loan_transactions.branch_id', '=', $branch_id);

        if (request('user') != 0) {
            $iterations->where('loan_transactions.created_by', '=', request('user'));
        }

        $iterations = $iterations->groupBy('loan_calculation_histories.id')->get();

        $transactions = LoanTransaction::whereIn('used', $iterations->pluck('id'));
        if (request('transaction') != 0) {
            $transactions->where('payment_type', '=', request('transaction'));
            $transactions->where(function ($query) {
                $query->where('amount', '>', 0)->orWhere('cash_back_amount', '>', 0);
            });
        }
        $transactions = $transactions->get()->groupBy('used');

        $payment_types = config('site.payment_types');
        $transaction_datas = [];
        foreach ($iterations as $key => $value) {
            $element_transactions = collect([]);
            if (isset($transactions[$value->id])) {
                $element_transactions = $transactions[$value->id];
            }
            if ($element_transactions->count() > 0) {
                $element = [];
                $element['user_id'] = $value->user_id;
                $element['sort_date_time'] = $value->created_at;
                $element['group_by_date'] = Helper::date_to_current_timezone($value->created_at, null, 'Y-m-d');
                $element['date'] = Helper::date_to_current_timezone($value->created_at);
                $element['time'] = Helper::time_to_current_timezone($value->created_at, null, 'H:i');
                $element['id'] = $value->id;
                $element['trx_id'] = Helper::date_to_current_timezone($value->created_at, null, 'Ymd');
                $element['username'] = $value->username;

                $element['transaction_type'] = 'Wallet Balance';
                $element['type'] = 'minus';
                $element['created_username'] = $value->transaction_username;

                foreach ($element_transactions as $k => $v) {
                    if ($v['payment_type'] != null) {
                        $element[$payment_types[$v['payment_type']] . ' In'] = Helper::numberShowing($v['amount'], $country_id);
                        $element[$payment_types[$v['payment_type']] . ' Out'] = Helper::numberShowing($v['cash_back_amount'], $country_id);
                    }
                }
                $element['Total'] = $element_transactions->sum('amount') - $element_transactions->sum('cash_back_amount');

                $transaction_datas[] = $element;
            }
        }

        if (request('credit') == 0 || request('credit') == 1) {
            $iterations = LoanCalculationHistory::select('loan_calculation_histories.*', DB::raw('concat(users.firstname," ",users.lastname) as username'),
                DB::raw('concat(created_user.firstname," ",created_user.lastname) as created_username'))
                ->join('loan_applications', 'loan_applications.id', '=', 'loan_calculation_histories.loan_id')
                ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
                ->leftJoin('loan_status_histories', function ($join) {
                    $join->on('loan_status_histories.loan_id', '=', 'loan_calculation_histories.loan_id')
                        ->where('loan_status_histories.status_id', '=', 4)
                        ->limit(1);
                })
                ->leftJoin('users as created_user', 'created_user.id', '=', 'loan_status_histories.user_id')
                ->where('loan_calculation_histories.created_at', '>=', $start_date)
                ->where('loan_calculation_histories.created_at', '<=', $end_date)
                ->whereIn('transaction_name', ['Loan Start']);
            if ($country_id != null) {
                $iterations->where('users.country', '=', $country_id);
            }
            if (request('user') != 0) {
                $iterations->where('loan_status_histories.user_id', '=', request('user'));
            }

            $iterations = $iterations->groupBy('loan_calculation_histories.id')->get();

            foreach ($iterations as $key => $value) {
                $element = [];
                $element['user_id'] = $value->user_id;
                $element['sort_date_time'] = $value->created_at;
                $element['group_by_date'] = Helper::date_to_current_timezone($value->created_at, null, 'Y-m-d');
                $element['date'] = Helper::date_to_current_timezone($value->created_at);
                $element['time'] = Helper::time_to_current_timezone($value->created_at, null, 'H:i');
                $element['id'] = $value->id;
                $element['trx_id'] = Helper::date_to_current_timezone($value->created_at, null, 'Ymd');
                $element['username'] = $value->username;

                $element['transaction_type'] = 'Wallet Credit';
                $element['type'] = 'plus';
                $element['created_username'] = $value->created_username;

                $element['Vault Out'] = $value->principal - $value->origination;

                $element['Total'] = 0;

                $transaction_datas[] = $element;
            }
        }

        $credits = Credit::select('credits.*', DB::raw('concat(users.firstname," ",users.lastname) as username'),
            DB::raw('concat(created_user.firstname," ",created_user.lastname) as created_username'))
            ->leftJoin('credit_status_histories', function ($join) {
                $join->on('credit_status_histories.credit_id', '=', 'credits.id')
                    ->where('credit_status_histories.status_id', '=', 3)
                    ->limit(1);
            })
            ->leftJoin('users', 'users.id', '=', 'credits.user_id')
            ->leftJoin('users as created_user', 'created_user.id', '=', 'credit_status_histories.user_id')
            ->where('credits.created_at', '>=', $start_date)
            ->where('credits.created_at', '<=', $end_date)
            ->where('credits.payment_type', '=', 1)
            ->where('credits.status', '=', 3);
        if (request('user') != 0) {
            $credits->where('credit_status_histories.user_id', '=', request('user'));
        }
        $credits = $credits->groupBy('credits.id')->get();

        $transactions = Wallet::whereIn('used', $credits->pluck('id'))->get()->groupBy('used');

        foreach ($credits as $key => $value) {
            $element = [];
            $element['user_id'] = $value->user_id;
            $element['sort_date_time'] = $value->created_at;
            $element['group_by_date'] = Helper::date_to_current_timezone($value->created_at, null, 'Y-m-d');
            $element['date'] = Helper::date_to_current_timezone($value->created_at);
            $element['time'] = Helper::time_to_current_timezone($value->created_at, null, 'H:i');
            $element['id'] = $value->id;
            $element['trx_id'] = Helper::date_to_current_timezone($value->created_at, null, 'Ymd');
            $element['username'] = $value->username;

            $element['transaction_type'] = 'Cash Withdrawal';
            $element['type'] = 'minus';
            $element['created_username'] = $value->created_username;

            $element_transactions = [];
            if (isset($transactions[$value->id])) {
                $element_transactions = $transactions[$value->id];
            }
            foreach ($element_transactions as $k => $v) {
                if ($v['type'] != null) {
                    if (in_array($v['type'], [3, 4, 5, 6])) {
                        $element[$payment_types[$v['type']] . ' In'] = Helper::numberShowing($v['amount'], $country_id);
                    } else {
                        $element[$payment_types[$v['type']] . ' Out'] = Helper::numberShowing($v['amount'], $country_id);
                    }
                }
            }
            $element['Total'] = $element_transactions->sum('amount');

            $transaction_datas[] = $element;
        }

        $transaction_datas = collect($transaction_datas)->sortBy('sort_date_time')->groupBy('group_by_date');

        $branch = Branch::find($branch_id);

        $data = [
            'branch'           => $branch->title,
            'country'          => $country,
            'start_date'       => $start_date,
            'end_date'         => $end_date,
            'transaction_data' => $transaction_datas,
            'main_start_date'  => $main_start_date,
            'main_end_date'    => $main_end_date,
        ];

        $pdf = \PDF::loadHTML(self::pdfHtml($data, $country_id, $branch_id));

        $filename = 'Cash tracking report ' . time() . '.pdf';

        $pdf->setPaper('a4', 'landscape')->save(public_path('pdf/' . $filename));

        $data = [];

        $data['url'] = asset('pdf/' . $filename);

        return $data;
    }

    public function pdfHtml($data, $country_id, $branch)
    {
        $html = '<html>
                    <head>
                        <style>.page_break { page-break-before: always; }</style>
                        <style>
                            page {
                                display: block;
                                margin: 0px;
                                background-color: #fff;
                                -webkit-print-color-adjust: exact;
                            }
                        
                            page[size="A4"] {
                                padding: 20px;
                                margin: 0px auto;
                            }
                        
                            header {
                                padding: 0;
                                margin-bottom: 20px;
                            }
                        
                            #logo {
                                text-align: center;
                            }
                        
                            header h1 {
                                margin: 0px;
                                text-align: center;
                                border-top: 1px solid #dadada;
                                /*border-bottom: 1px solid #dadada;*/
                                padding: 10px 0;
                                color: #555;
                            }
                        
                            .project-info {
                                text-align: center;
                            }
                        
                            .project-info h1 {
                                font-size: 40px;
                                font-weight: 700;
                                text-align: center;
                            }
                        
                            .project-info span {
                                font-size: 25px;
                                margin-top: 20px;
                            }
                        
                            #logo img {
                                width: 120px;
                            }
                        
                            table {
                                padding: 10px;
                                color: #000;
                                width: 100%;
                                border-collapse: collapse;
                                border: 1px solid #dadada;
                            }
                        
                            table + table {
                                margin-top: -1px;
                            }
                        
                            /* table thead {
                                border-bottom: 1px solid rgba(0, 0, 0, 0.3);
                            } */
                        
                            table thead th {
                                font-weight: 500;
                                font-size: 14px;
                                padding: 5px 10px;
                                text-align: left;
                                color: #555;
                                border: 1px solid #dadada;
                            }
                        
                            table tr td {
                                padding: 5px 10px;
                                font-size: 13px;
                                color: #555;
                                border-collapse: collapse;
                                border: 1px solid #dadada;
                            }
                        
                            table tr a {
                                color: #555;
                                text-decoration: none;
                            }
                        
                            .content {
                                padding-top: 10px;
                                padding: 50px;
                            }
                        
                            .table tr td:nth-child(1), .table tr td:nth-child(2), .table tr td:nth-child(3) {
                                /**  width:20%; **/
                            }
                        
                            .table-box {
                                margin: 20px 0;
                            }
                        
                            .table-text label {
                                font-size: 14px;
                                font-weight: 500;
                                color: #000;
                            }
                        
                            .table-text span {
                                font-size: 14px;
                                font-weight: 600;
                                color: #555;
                            }
                        
                            caption {
                                padding: 5px 10px;
                                color: #555;
                                text-align: left;
                                font-size: 16px;
                                border: 1px solid #dadada;
                                border-bottom: none;
                                font-weight: 600;
                                text-transform: uppercase;
                                background: #eaeaea;
                                width: 100%;
                            }
                        
                            .project-category span, .project-category label {
                                font-size: 30px;
                            }
                        
                            .table-header tr td {
                                font-weight: 600;
                                color: #222;
                            }
                        
                            .table-header td {
                                font-weight: 600;
                                color: #555;
                                font-size: 16px;
                                background: #eaeaea;
                            }
                        
                            .table-footer td {
                                background: #f1f1f1;
                                font-size: 14px;
                                color: #333;
                                font-weight: 600;
                            }
                        
                            /*.table-indicator tr td:nth-child(3) {*/
                            /*width: 40%;*/
                            /*}*/
                        
                            .table-break {
                                border-top: 2px solid #dadada;
                                padding-top: 30px;
                                margin-top: 30px;
                            }
                        
                            @media print {
                                body {
                                    background: #525659;
                                    font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
                                    margin: 0px;
                                }
                        
                                .project-info {
                                    margin-top: 350px;
                                }
                        
                                .project-info h1 {
                                    font-size: 50px;
                                }
                        
                                .project-info span {
                                    font-size: 30px;
                                }
                        
                                .project-category span, .project-category label {
                                    font-size: 30px;
                                }
                        
                                page {
                                    display: block;
                                    margin: 0;
                                    background-color: #fff;
                                    -webkit-print-color-adjust: exact;
                                }
                        
                                .table-break {
                                    page-break-before: always;
                                    border-bottom: 1px solid #dadada;
                                }
                        
                                table {
                                    overflow: visible !important;
                                    width: 100%;
                                }
                        
                                tr {
                                    page-break-inside: avoid
                                }
                        
                                .content {
                                    padding: 0px;
                                    padding-top: 10px;
                                }
                        
                                caption {
                                    width: 100%;
                                }
                            }
                        </style>
                    </head>
                    <body>';

        $dates = Helper::getDatesFromRange($data['main_start_date'], $data['main_end_date']);
        foreach ($dates as $element) {
            $transaction_data = '';
            $balance = 0;
            if (isset($data['transaction_data'][$element])) {
                foreach ($data['transaction_data'][$element] as $k => $value) {
                    $transaction_data .= '<tr>
                        <td>' . $value['trx_id'] . $value['id'] . '</td>
                        <td>' . $value['time'] . '</td>
                        <td>' . $value['transaction_type'] . '</td>
                        <td>' . $value['username'] . '</td>';
                    if (request('transaction') == 1 || request('transaction') == 0) {
                        if (isset($value['Vault Out'])) {
                            $transaction_data .= '<td>' . $value['Vault Out'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                        if (isset($value['Vault In'])) {
                            $transaction_data .= '<td>' . $value['Vault In'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                    }
                    if (request('transaction') == 3 || request('transaction') == 0) {
                        if (isset($value['Vault door'])) {
                            $transaction_data .= '<td>' . $value['Vault door In'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                    }
                    if (request('transaction') == 2 || request('transaction') == 0) {
                        if (isset($value['Petty cash Out'])) {
                            $transaction_data .= '<td>' . $value['Petty cash Out'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                        if (isset($value['Petty cash In'])) {
                            $transaction_data .= '<td>' . $value['Petty cash In'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                    }
                    if (request('transaction') == 4 || request('transaction') == 0) {
                        if (isset($value['Maestro In'])) {
                            $transaction_data .= '<td>' . $value['Maestro In'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                    }
                    if (request('transaction') == 6 || request('transaction') == 0) {
                        if (isset($value['Cheque In'])) {
                            $transaction_data .= '<td>' . $value['Cheque In'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                    }
                    if (request('transaction') == 5 || request('transaction') == 0) {
                        if (isset($value['Bank Transfer In'])) {
                            $transaction_data .= '<td>' . $value['Bank Transfer In'] . '</td>';
                        } else {
                            $transaction_data .= '<td>0</td>';
                        }
                    }
                    if ($value['type'] == 'minus') {
                        $balance = $balance - $value['Total'];
                    } else {
                        $balance = $balance + $value['Total'];
                    }
                    if (request('transaction') == 0) {
                        $transaction_data .= '<td>' . $balance . '</td>';
                    }
                    $transaction_data .= '<td>' . $value['created_username'] . '</td>';
                    $transaction_data .= '</tr>';
                }
            }

            $columns = '';

            if (request('transaction') == 1 || request('transaction') == 0) {
                $columns .= '<th>Vault Out</th>
                        <th>Vault In</th>';
            }
            if (request('transaction') == 3 || request('transaction') == 0) {
                $columns .= '<th>Vault Door In</th>';
            }
            if (request('transaction') == 2 || request('transaction') == 0) {
                $columns .= '<th>Petty Cash Out</th>
                        <th>Petty Cash In</th>';
            }
            if (request('transaction') == 4 || request('transaction') == 0) {
                $columns .= '<th>Maestro</th>';
            }
            if (request('transaction') == 6 || request('transaction') == 0) {
                $columns .= '<th>Cheque</th>';
            }
            if (request('transaction') == 5 || request('transaction') == 0) {
                $columns .= '<th>Bank Transfer</th>';
            }
            if (request('transaction') == 0) {
                $columns .= '<th>Balance</th>';
            }

            $audit_data = self::auditData($element, $country_id, $branch);

            $audit_report = '';

            $payment_types = config('site.payment_types');

            $statuses = [1, 3, 2, 4, 6, 5];

            foreach ($statuses as $status) {
                $next_date_dayopen = 0;
                if (isset($audit_data['next_date_dayopens'][$status])) {
                    $next_date_dayopen = $audit_data['next_date_dayopens'][$status];
                }
                $audit_report .= '<tr>
                        <td>' . $payment_types[$status] . '</td>
                        <td>' . $audit_data['dayopens'][$status] . '</td>
                        <td>' . $audit_data['in_amount'][$status] . '</td>
                        <td>' . $audit_data['out_amount'][$status] . '</td>
                        <td>' . $next_date_dayopen . '</td>
                        <td>' . $audit_data['difference'][$status] . '</td>
                    </tr>';
            }
            $audit_report .= '<tr>
                        <td>Total</td>
                        <td>' . $audit_data['dayopen_sum'] . '</td>
                        <td>' . $audit_data['total_in'] . '</td>
                        <td>' . $audit_data['total_out'] . '</td>
                        <td>' . $audit_data['next_dayopen_sum'] . '</td>
                        <td>' . $audit_data['total_difference'] . '</td>
                    </tr>';

            $html .= '
                        <div style="display: inline-flex;">
                            <div style="text-align: left">
                                <h4>Cash Tracking Report</h4>
                                ' . $element . ' To ' . $element . '
                            </div>
                            <div style="text-align: right">
                               ' . $data['branch'] . '<br> 
                            </div>
                        </div>
                        <hr> 
                        <table>
                            <thead style="border-bottom: 1px #000000 solid;">
                                <tr>
                                    <th>Cash Trx Id</th>
                                    <th>Time</th>
                                    <th>Transaction Type</th>
                                    <th>Client Name</th>
                                    ' . $columns . '
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $transaction_data . '
                            </tbody>
                        </table>
                        <div class="page_break"></div>
                        <div style="display: inline-flex;">
                            <div style="text-align: left">
                                <h4>Cash Tracking Report</h4>
                                ' . $element . ' To ' . $element . '
                            </div>
                            <div style="text-align: right">
                               ' . $data['branch'] . '<br> 
                            </div>
                        </div>
                        <hr> 
                        <table>
                            <thead>
                                <tr>
                                    <th>' . $element . ' To ' . $element . '</th>
                                    <th>Begin Balance</th>
                                    <th>Out</th>
                                    <th>In</th>
                                    <th>End Balance</th>
                                    <th>Difference</th>
                                </tr>
                            </thead>
                            <tbody>
                                ' . $audit_report . '
                            </tbody>
                        </table>
                        <div class="page_break"></div>';
        }


        $html .= '<script type="text/php">

    if ( isset($pdf) ) {
        $x = 72;
        $y = 550;
        $text = "' . $data['branch'] . ' - Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->get_font("Arial", "bold");
        $size = 12;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }

</script></body>
                </html>';
        return $html;
    }

    public function auditData($date, $country_id, $branch)
    {
        $dayopen = Dayopen::whereDate('date', '=', $date)->first();

        $payment_types = [1, 2, 3, 4, 5, 6];

        $data['dayopens'] = Dayopen::whereDate('date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->pluck('amount', 'payment_type');

        $end_date = Dayopen::where('date', '>', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->orderBy('date', 'asc')
            ->first();

        $data['loan_transactions'] = LoanTransaction::select('payment_type', DB::raw('sum(amount - cash_back_amount) as amount'))
            ->where('payment_date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->groupBy('payment_type')
            ->pluck('amount', 'payment_type');

        $data['credits'] = Credit::select('payment_type', DB::raw('sum(amount) as amount'))
            ->whereDate('created_at', '=', $date)
            ->whereIn('payment_type', $payment_types)
            ->where('branch_id', '=', $branch)
            ->groupBy('payment_type')
            ->pluck('amount', 'payment_type');

        $date = null;
        $data['is_eligible'] = false;
        $data['end_date'] = '';
        if ($end_date != null) {
            $data['is_eligible'] = true;
            $date = $end_date->date;
            $data['end_date'] = Helper::datebaseToFrontDate($date);
        }

        $data['next_date_dayopens'] = Dayopen::whereDate('date', '=', $date)
            ->where('branch_id', '=', $branch)
            ->whereIn('payment_type', $payment_types)
            ->pluck('amount', 'payment_type');

        $data['difference'] = [];
        $data['in_amount'] = [];
        $data['out_amount'] = [];
        foreach ($payment_types as $key => $value) {

            if (isset($data['loan_transactions'][$value]) && isset($data['nlb_in'][$value])) {
                $data['in_amount'][$value] = round($data['loan_transactions'][$value], 2) + round($data['nlb_in'][$value], 2);
            } else if (isset($data['loan_transactions'][$value])) {
                $data['in_amount'][$value] = $data['loan_transactions'][$value];
            } else if (isset($data['nlb_in'][$value])) {
                $data['in_amount'][$value] = $data['nlb_in'][$value];
            } else {
                $data['in_amount'][$value] = '0.00';
            }

            if (isset($data['credits'][$value]) && isset($data['nlb_out'][$value])) {
                $data['out_amount'][$value] = round($data['credits'][$value], 2) + round($data['nlb_out'][$value], 2);
            } else if (isset($data['credits'][$value])) {
                $data['out_amount'][$value] = $data['credits'][$value];
            } else if (isset($data['nlb_out'][$value])) {
                $data['out_amount'][$value] = $data['nlb_out'][$value];
            } else {
                $data['out_amount'][$value] = '0.00';
            }

            if (!isset($data['dayopens'][$value])) {
                $data['dayopens'][$value] = 0;
            }
            if (!isset($data['in_amount'][$value])) {
                $data['in_amount'][$value] = 0;
            }
            if (!isset($data['out_amount'][$value])) {
                $data['out_amount'][$value] = 0;
            }

            $data['difference'][$value] = round($data['dayopens'][$value], 2) - round($data['out_amount'][$value], 2) + round($data['in_amount'][$value], 2);


            $data['in_amount'][$value] = Helper::decimalShowing($data['in_amount'][$value], $country_id);
            $data['out_amount'][$value] = Helper::decimalShowing($data['out_amount'][$value], $country_id);
            $data['difference'][$value] = Helper::decimalShowing($data['difference'][$value], $country_id);
        }

        $data['dayopen_sum'] = round($data['dayopens']->sum(), 2);
        $data['total_in'] = Helper::decimalShowing($data['loan_transactions']->sum(), $country_id);
        $data['total_out'] = Helper::decimalShowing($data['credits']->sum(), $country_id);
        $data['next_dayopen_sum'] = $data['next_date_dayopens']->sum();
        $data['total_difference'] = Helper::decimalShowing(round($data['dayopen_sum'], 2) - round($data['credits']->sum(), 2) + round($data['loan_transactions']->sum(), 2),
            $country_id);
        $data['next_dayopen_sum'] = Helper::decimalShowing($data['next_dayopen_sum'], $country_id);
        $data['dayopen_sum'] = Helper::decimalShowing($data['dayopens']->sum(), $country_id);
        $data['dayopens'] = $data['dayopens']->map(function ($item, $key) use ($country_id) {
            return Helper::decimalShowing($item, $country_id);
        });
        $data['next_date_dayopens'] = $data['next_date_dayopens']->map(function ($item, $key) use ($country_id) {
            return Helper::decimalShowing($item, $country_id);
        });
        return $data;
    }

}
