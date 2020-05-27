<?php

namespace App\Http\Controllers\Admin1;

use App\Events\MessageRefresh;
use App\Http\Controllers\Controller;
use App\Library\Helper;
use App\Models\Country;
use App\Models\LoanApplication;
use App\Models\Message;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|admin')->except('getCallback');
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

        return view('admin1.pages.messages.index', $data);
    }

    public function store()
    {
        $this->validate(request(), [
            'message' => 'required',
        ]);
        $users = '';
        if (request('user_id') && count(request('user_id')) > 0) {
            $users = request('user_id');
        } else if (request('country_id')) {
            $users = User::where('users.role_id', '=', 3)
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
                $users = User::where('users.role_id', '=', 3);
            } else {
                $users = User::where('users.role_id', '=', 3)->where('users.country', '=', auth()->user()->country);
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
            $user = User::find($user);
            if ($user != null) {
                $country = Country::find($user->country);
                if ($country != null) {
                    $mobile_no = null;
                    $user_infos = UserInfo::where('user_id', '=', $user->id)->whereIn('type', [1, 2])->get();
                    $cellphones = $user_infos->where('type', '=', 2);
                    if ($cellphones->count() > 0) {
                        $mobile_no = $cellphones->first()->value;
                    }
                    if ($mobile_no == null) {
                        $telephones = $user_infos->where('type', '=', 1);
                        if ($telephones->count() > 0) {
                            $mobile_no = $telephones->first()->value;
                        }
                    }
                    if ($mobile_no != null) {
                        $response = \App\Library\Message::sendSMS('+' . $country->country_code . $mobile_no, request('message'), $country->sender_number);
                        $message = Message::create([
                            'user_id' => $user->id,
                            'message' => request('message'),
                        ]);
                        if (isset($response['sid'])) {
                            $inputs = [
                                'json' => json_encode($response),
                                'sid'  => $response['sid'],
                            ];
                            if (isset($response['SmsStatus'])) {
                                $inputs['status'] = $response['SmsStatus'];
                            }
                            $message->update($inputs);
                        }
                    }
                }
            }
        }

        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function indexDatatable()
    {
        $selection = [
            'messages.id',
            DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
            'messages.message',
            'messages.status',
            'messages.created_at'
        ];
        $messages = Message::select($selection)
            ->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'messages.user_id')
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

        $data['users'] = User::select(DB::raw('concat(users.firstname," ",users.lastname,"(",users.email,")") as user_name'), 'users.id');
        if (!request('type') || request('type') != 'all') {
            $data['users']->join('user_logins', 'user_logins.user_id', '=', 'users.id');
        }
        $data['users']->where('users.role_id', '=', 3);

        if ($country != '' && $country != 0) {
            $data['users'] = $data['users']->where('users.country', '=', $country);
        }

        if (request('status') && request('status') != '') {
            $data['users']->where('users.status', '=', request('status'));
        }
        if (request('loan') == 1) {
            $clients = LoanApplication::whereIn('loan_status', [1, 2, 3, 4, 5, 6, 12])->pluck('client_id');
            $data['users']->whereIn('users.id', $clients);
        } else if (request('loan') == 2) {
            $clients = LoanApplication::whereIn('loan_status', [1, 2, 3, 4, 5, 6, 12])->pluck('client_id');
            $data['users']->whereNotIn('users.id', $clients);
        }
        $data['users'] = $data['users']->pluck('users.user_name', 'id');

        return $data;
    }

    public function getCallback()
    {
        Log::info('Message response from twilli');
        Log::info(request()->all());
        $message = Message::where('sid', '=', request('SmsSid'))
            ->update([
                'status' => request('SmsStatus')
            ]);
        event(new MessageRefresh());
        return 'true';
    }
}
