<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\FirebaseHelper;
use App\Library\Helper;
use App\Models\Country;
use App\Models\FirebaseNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MessageController extends Controller
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

        return view('admin.messages.index', $data);
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
        } elseif (request('country_id')) {
            $users = User::where('users.role_id', '=', 3)
                ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
                ->where('users.country', '=', request('country_id'))
                ->pluck('users.id');
        } else {
            if (auth()->user()->hasRole('super admin')) {
                $users = User::where('users.role_id', '=', 3)
                    ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
                    ->pluck('users.id');
            } else {
                $users = User::where('users.role_id', '=', 3)
                    ->join('user_logins', 'user_logins.user_id', '=', 'users.id')
                    ->where('users.country', '=', auth()->user()->country)
                    ->pluck('users.id');
            }
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
            ->where('users.role_id', '=', 3)
            ->where('users.country', '=', $country)
            ->pluck('users.user_name', 'id');

        return $data;
    }
}
