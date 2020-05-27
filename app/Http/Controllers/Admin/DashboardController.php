<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Credit;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Models\UserTerritory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|processor|auditor|debt collector|loan approval|credit and processing')
            ->only([
                'branch',
                'branchStore'
            ]);
    }

    public function index()
    {
        $data = [];

        if (auth()->user()->hasRole('super admin|admin|auditor')) {
            $userCount = User::where(['role_id' => '3'])->count();
            $loanCount = LoanApplication::count();
            $pendingCount = LoanApplication::where('loan_status', 1)->count();
            $territory = UserTerritory::select('*');
            if (!auth()->user()->hasRole('super admin')) {
                $territory->where('id', '=', auth()->user()->territory);
            }
            $territory = $territory->get();
            $data = [
                'userCount'     => $userCount,
                'loanCount'     => $loanCount,
                'pendingCount'  => $pendingCount,
                'territory'     => $territory,
                'payment_types' => config('site.payment_types')
            ];
            $today = Carbon::today()->format('Y-m-d');
            if (request('date')) {
                $today = date('Y-m-d', strtotime(request('date')));
            }
            $loan_amounts = LoanTransaction::select('loan_transactions.*', 'users.territory')
                ->leftJoin('loan_applications', 'loan_applications.id', '=', 'loan_transactions.loan_id')
                ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id')
                ->whereDate('loan_transactions.created_at', '=', $today)
                ->get();
            $credits = Credit::select('credits.*', 'users.territory')
                ->leftJoin('users', 'users.id', '=', 'credits.user_id')
                ->whereDate('credits.created_at', '=', $today)
                ->get();
            /*$daily_turnover = DailyTurnover::whereDate('created_at', '=', $today)
                ->get();*/
            foreach ($territory as $item) {
                $users = User::where(['territory' => $item->id])->where(['role_id' => '3'])->get()->pluck(['id']);
                $applications = LoanApplication::whereIN('client_id', $users)->get();
                $openApplications = LoanApplication::whereIN('client_id', $users)
                    ->whereIN('loan_status', ['2'])
                    ->select([
                        DB::raw("SUM(amount) as balance"),
                        DB::raw("COUNT(id) as totalApplications")
                    ])
                    ->first();
                $pendingApplications = LoanApplication::whereIN('client_id', $users)
                    ->where('loan_status', 1)
                    ->get();
                $exceeding = LoanApplication::whereIN('client_id', $users)
                    ->where('deadline_date', '<', $today)
                    ->where('loan_status', '=', 1)
                    ->select([
                        DB::raw("SUM(amount) as balance"),
                        DB::raw("COUNT(id) as totalApplications")
                    ])
                    ->first();

                $item->userCount = count($users);
                $item->applicationsCount = count($applications);
                $item->pendingCount = count($pendingApplications);

                $payments = [];
                $turnover = [];

                foreach (config('site.payment_types') as $key => $value) {
                    $payments[$key]['in'] = $loan_amounts->where('territory', '=', $item->id)
                        ->where('payment_type', '=', $key)
                        ->sum('amount');
                    $type = $key;
                    if ($type == 5) {
                        $type = 2;
                    } elseif ($type == 2) {
                        $type = 5;
                    }
                    $credit = 0;
                    if ($type == 1 || $type == 2) {
                        $credit = $credits->where('territory', '=', $item->id)
                            ->where('payment_type', '=', $type)->sum('amount');
                    }
                    $payments[$key]['out'] = $credit;

                    /* $turnover[$key]['in'] = $daily_turnover->where('territory_id', '=', $item->id)
                         ->where('payment_type', '=', $key)
                         ->where('direction', '=', 1)
                         ->where('transaction_type', '=', 1)
                         ->sum('amount');
                     $turnover[$key]['out'] = $daily_turnover->where('territory_id', '=', $item->id)
                         ->where('payment_type', '=', $key)
                         ->where('direction', '=', 2)
                         ->where('transaction_type', '=', 1)
                         ->sum('amount');

                     $correction[$key]['in'] = $daily_turnover->where('territory_id', '=', $item->id)
                         ->where('payment_type', '=', $key)
                         ->where('direction', '=', 1)
                         ->where('transaction_type', '=', 2)
                         ->sum('amount');
                     $correction[$key]['out'] = $daily_turnover->where('territory_id', '=', $item->id)
                         ->where('payment_type', '=', $key)
                         ->where('direction', '=', 2)
                         ->where('transaction_type', '=', 2)
                         ->sum('amount');*/
                }
                $item->loan_amounts = $payments;
                $item->turnovers = $turnover;
//            $item->correction = $correction;


                if ($openApplications) {
                    $item->openCount = $openApplications->totalApplications;
                    $item->openBalance = $openApplications->balance;
                } else {
                    $item->openCount = 0;
                    $item->openBalance = 0;
                }
                if ($exceeding) {
                    $item->exceedingCount = $exceeding->totalApplications;
                    $item->exceedingBalance = $exceeding->balance;
                } else {
                    $item->exceedingCount = 0;
                    $item->exceedingBalance = 0;
                }
            }
            if (request('date')) {
                return $data;
            }
        }
        return view('admin.dashboard', $data);
    }

    public function branch()
    {
        $data = [];
        $data['branches'] = auth()->user()->userBranches->pluck('title', 'id');
        return view('admin.branch.select', $data);
    }

    public function branchStore()
    {
        $this->validate(request(), ['branch_id' => 'required'], ['branch_id.required' => 'The branch field selection is required']);

        session(['branch_id' => request('branch_id')]);
        $branch = Branch::find(request('branch_id'));
        session(['branch_name' => $branch->title]);
        return redirect('admin/dashboard');
    }
}
