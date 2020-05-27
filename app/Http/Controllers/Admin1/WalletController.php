<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:super admin|admin');
    }

    public function index()
    {
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
        } else {
            $country = auth()->user()->country;
        }

        $data = [];
        $users = User::whereIn('role_id', [1, 2, 5, 6, 9])
            ->select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'id');
        if ($country != '') {
            $users->where('country', '=', $country);
        }
        $users = $users->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();
        $data['users'] = $users;

        return view('admin1.pages.wallet.index', $data);
    }

    public function query()
    {
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
        } else {
            $country = auth()->user()->country;
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

        $selections = [
            'wallets.created_at as date_time',
            'wallets.amount',
            'lch.loan_id',
            'lch.payment_amount',
            'lch.commission_percent',
            DB::raw('concat(collector.firstname," ",collector.lastname) as collector'),
            DB::raw('concat(create.firstname," ",create.lastname) as created_by_user'),
            DB::raw('concat(client.firstname," ",client.lastname) as client_user'),
        ];

        $users = User::whereNotIn('role_id', [3, 4])
            ->select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'id');
        if ($country != '') {
            $users->where('country', '=', $country);
        }
        $users = $users->orderBy('name', 'asc')
            ->pluck('id');

        $wallets = Wallet::select($selections)
            ->leftJoin('users as collector', 'collector.id', '=', 'wallets.user_id')
            ->leftJoin('users as create', 'create.id', '=', 'wallets.created_by')
            ->leftJoin('loan_calculation_histories as lch', 'lch.id', '=', 'wallets.history_id')
            ->leftJoin('loan_applications', 'loan_applications.id', '=', 'lch.loan_id')
            ->leftJoin('users as client', 'client.id', '=', 'loan_applications.client_id')
            ->whereIn('wallets.user_id', $users);
        if (request('collector_id')) {
            $wallets->where('wallets.user_id', '=', request('collector_id'));
        }
        if (request('start_date')) {
            $wallets->where('wallets.created_at', '>=', $start_date);
        }
        if (request('end_date')) {
            $wallets->where('wallets.created_at', '<=', $end_date);
        }

        $wallets->groupBy('wallets.id');

        return $wallets;
    }

    public function indexDatatable()
    {
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : null;
        } else {
            $country = auth()->user()->country;
        }
        return DataTables::of($this->query())
            ->filterColumn('client_user', function ($query, $keyword) {
                $sql = "concat(client.firstname,' ',client.lastname)  like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('collector', function ($query, $keyword) {
                $sql = "concat(collector.firstname,' ',collector.lastname)  like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('created_by_user', function ($query, $keyword) {
                $sql = "concat(create.firstname,' ',create.lastname)  like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('date', function ($row) {
                return Helper::date_time_to_current_timezone($row->date_time);
            })
            ->addColumn('amount', function ($row) use ($country) {
                return Helper::decimalShowing(abs($row->amount), $country);
            })
            ->make(true);
    }

    public function store()
    {
        $user = User::find(request('user_id'));
        $max = 0;
        if ($user != null) {
            $max = $user->userBalance();
        }
        $this->validate(request(), [
            'user_id' => 'required',
            'amount'  => 'required|numeric|min:0.01|max:' . $max,
        ]);

        Wallet::create([
            'user_id' => request('user_id'),
            'amount'  => '-' . request('amount'),
            'note'    => request('note'),
        ]);
        $data = [];
        $data['status'] = true;
        return $data;
    }

}
