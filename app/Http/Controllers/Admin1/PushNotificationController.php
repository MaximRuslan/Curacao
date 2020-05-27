<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\FirebaseHelper;
use App\Library\Helper;
use App\Models\Country;
use App\Models\FirebaseNotification;
use App\Models\LoanApplication;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PushNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin');
    }

    public function index()
    {
        $data = [];

        if (auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::pluck('name', 'id');
            $data['users'] = [];
        } else {
            $data['users'] = User::select(DB::raw('concat(users.firstname," ",users.lastname,"(",users.email,")") as user_name'), 'users.id')
                ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
                ->where('users.role_id', '=', 3)
                ->where('users.country', '=', auth()->user()->country)
                ->pluck('user_name', 'id');
        }
        $statuses = UserStatus::select('id', 'title', 'role')
            ->orderBy('title', 'asc')
            ->get();
        $data['statuses'] = $statuses->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        })->pluck('title', 'id');

        $data['loans'] = config('site.loan_statuses');

        return view('admin1.pages.push_notifications.index', $data);
    }

    public function store()
    {
        $this->validate(request(), [
            'title' => 'required',
            'body'  => 'required'
        ]);
        $users = '';
        if (request('user_id') && count(request('user_id')) > 0) {
            $users = request('user_id');
        } else if (request('country_id')) {
            $users = User::where('users.role_id', '=', 3)
                ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
                ->where('users.country', '=', request('country_id'));
            if (request('status') && request('status') != '') {
                $users->where('users.status', '=', request('status'));
            }
            if (request('loan') == 1) {
                $clients = LoanApplication::whereIn('loan_status', [1, 2, 3, 4, 5, 6, 12])->pluck('client_id');
                $users->whereIn('users.id', $clients);
            } else if (request('loan') == 2) {
                $clients = LoanApplication::whereIn('loan_status', [1, 2, 3, 4, 5, 6, 12])->pluck('client_id');
                $users->whereNotIn('users.id', $clients);
            }
            $users = $users->pluck('users.id');
        } else {
            if (auth()->user()->hasRole('super admin')) {
                $users = User::where('users.role_id', '=', 3)
                    ->join('user_logins', 'user_logins.user_id', '=', 'users.id');
            } else {
                $users = User::where('users.role_id', '=', 3)
                    ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
                    ->where('users.country', '=', auth()->user()->country);
            }
            if (request('status') && request('status') != '') {
                $users->where('users.status', '=', request('status'));
            }
            if (request('loan') == 1) {
                $clients = LoanApplication::whereIn('loan_status', [1, 2, 3, 4, 5, 6, 12])->pluck('client_id');
                $users->whereIn('users.id', $clients);
            } else if (request('loan') == 2) {
                $clients = LoanApplication::whereIn('loan_status', [1, 2, 3, 4, 5, 6, 12])->pluck('client_id');
                $users->whereNotIn('users.id', $clients);
            }
            $users = $users->pluck('users.id');
        }
        $users = collect($users)->unique();
        foreach ($users as $user) {
            FirebaseHelper::firebaseNotification($user, request('title'), request('body'), 'broadcast', []);
        }

        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function indexDatatable()
    {
        $selection = [
            'firebase_notifications.id',
            DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
            'firebase_notifications.title',
            'firebase_notifications.body',
            'firebase_notifications.type',
            'firebase_notifications.created_at'
        ];
        $messages = FirebaseNotification::select($selection)
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'firebase_notifications.user_id')
                    ->whereNull('users.deleted_at');
            });

        return DataTables::of($messages)
            ->addColumn('user_name', function ($row) {
                return ucwords(strtolower($row->user_name));
            })
            ->addColumn('created_at', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->make(true);
    }

    public function getUsersFromCountry($country)
    {
        $data = [];

        $data['users'] = User::select(DB::raw('concat(users.firstname," ",users.lastname,"(",users.email,")") as user_name'), 'users.id')
            ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 3);

        if ($country != '' && $country != 0) {
            $data['users'] = $data['users']->where('users.country', '=', $country);
        }

        if (request('status') && request('status') != '') {
            $data['users']->where('users.status', '=', request('status'));
        }
        $data['users'] = $data['users']->pluck('users.user_name', 'id');

        return $data;
    }
}
