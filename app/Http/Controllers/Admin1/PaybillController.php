<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\EmailHelper;
use App\Mail\ConfirmEmail;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Documents;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserStatus;
use App\Models\UserTerritory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class PaybillController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        return view('admin1.pages.paybills.index');
    }

    public function create()
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        if (!auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', auth()->user()->country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('country_id', '=', auth()->user()->country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('country_id', '=', auth()->user()->country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
        } else if (auth()->user()->hasRole('super admin') && $country != '') {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', $country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
        } else {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->pluck('name', 'id');
        }
        $data['countries'] = $data['countries']->map(function ($item, $key) {
            return ucwords(strtolower($item));
        });
        if (isset($data['territories'])) {
            $data['territories'] = $data['territories']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        }
        if (isset($data['branches'])) {
            $data['branches'] = $data['branches']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        }

        $data['statuses'] = UserStatus::select('id', 'title', 'role')
            ->orderBy('title', 'asc')
            ->get();
        $data['statuses'] = $data['statuses']->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        });

        return view('admin1.pages.paybills.create', $data);
    }

    public function edit(User $user)
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';
        if (!auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', auth()->user()->country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('country_id', '=', auth()->user()->country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('country_id', '=', auth()->user()->country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
        } else if (auth()->user()->hasRole('super admin') && $country != '') {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', $country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
        } else {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->pluck('name', 'id');
        }
        $data['countries'] = $data['countries']->map(function ($item, $key) {
            return ucwords(strtolower($item));
        });
        if (isset($data['territories'])) {
            $data['territories'] = $data['territories']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        }
        if (isset($data['branches'])) {
            $data['branches'] = $data['branches']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        }

        $data['statuses'] = UserStatus::select('id', 'title', 'role')
            ->orderBy('title', 'asc')
            ->get();
        $data['statuses'] = $data['statuses']->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        });

        $data['user'] = $user;

        return view('admin1.pages.paybills.create', $data);
    }

    public function show(User $user)
    {
        $format = config('site.date_format.php');

        $documents = Documents::select('name', 'id', 'document')
            ->where('main_id', '=', $user->id)
            ->where('type', '=', '1')
            ->get();
        foreach ($documents as $key => $value) {
            $value->document = asset('uploads/' . $value->document);
        }

        if ($user->dob != null) {
            $user->dob = date($format, strtotime($user->dob));
        }
        if ($user->exp_date != null) {
            $user->exp_date = date($format, strtotime($user->exp_date));
        }
        if ($user->pp_exp_date != null) {
            $user->pp_exp_date = date($format, strtotime($user->pp_exp_date));
        }
        $country = Country::find($user->country);
        $country_code = 0;
        $phone_length = 10;
        if ($country != null) {
            $country_code = $country->country_code;
            if ($country->phone_length != null) {
                $phone_length = $country->phone_length;
            }
        }
        $filteredArr = [
            'id'               => ["type" => "hidden", 'value' => $user->id],
            'firstname'        => ["type" => "text", 'value' => $user->firstname],
            'lastname'         => ["type" => "text", 'value' => $user->lastname],
            'email'            => ["type" => "email", 'value' => $user->email],
            'address'          => ["type" => "textarea", 'value' => $user->address],
            'status'           => ["type" => "select2", 'value' => $user->status],
            'country'          => ["type" => 'select2', 'value' => $user->country],
            'territory'        => ['type' => 'select2', 'value' => $user->territory],
            'branches'         => ['type' => 'select2', 'value' => $user->userBranches->pluck('id')],
            'country_code'     => ['type' => 'text', 'value' => $country_code],
            'phone_length'     => ['type' => 'text', 'value' => $phone_length],
            'transaction_type' => ['type' => 'select2', 'value' => $user->transaction_type],
            'transaction_fee'  => ['type' => 'number', 'value' => $user->transaction_fee],
            'contact_person'   => ["type" => 'text', 'value' => $user->contact_person],
            'commission_type'  => ['type' => 'select2', 'value' => $user->commission_type],
            'commission_fee'   => ['type' => 'number', 'value' => $user->commission_fee],
        ];
        $telephones = UserInfo::where('user_id', '=', $user->id)->where('type', '=', 1)->pluck('value');
        $cellphones = UserInfo::where('user_id', '=', $user->id)->where('type', '=', 2)->pluck('value');
        $emails = UserInfo::where('user_id', '=', $user->id)->where('type', '=', 3)->get();
        return response()->json([
            "status"          => "success",
            "inputs"          => $filteredArr,
            'telephones'      => $telephones,
            'cellphones'      => $cellphones,
            'other_documents' => $documents,
            'emails'          => $emails
        ]);
    }

    public function store()
    {
        $inputs = request()->all();
        $format = config('site.date_format.php');

        $this->validate(request(), User::merchantValidationRules($inputs), ['primary.required' => "Minimum one primary email required."]);

        if (request('id') != '') {
            $user = User::find(request('id'));
        }

        $country = Country::find(request('country'));
        $length = 10;
        if ($country != null) {
            if ($country->phone_length != null) {
                $length = $country->phone_length;
            }
        }

        if ((!request('telephone') && !request('cellphone')) || ((request('telephone') && count(request('telephone')) == 0) && (request('cellphone') && count(request('cellphone')) == 0))) {
            $data = [];
            $data['status'] = false;
            $data['type'] = 'phone';
            $data['message'] = 'Minimum one telephone or cellphone required.';
            return $data;
        }


        if (!request('secondary_email') && count(request('secondary_email')) == 0) {
            $data = [];
            $data['status'] = false;
            $data['type'] = 'email';
            $data['message'] = 'Minimum one email required.';
            return $data;
        }

        if (request('territory') == 'Select District' || request('territory') == '') {
            $inputs['territory'] = null;
        }

        if (isset($user) && $user != null) {
            $user->update($inputs);
        } else {
            $inputs['password'] = '';
            $inputs['email'] = '';
            $inputs['is_verified'] = 0;
            $user = User::create($inputs);
            $user->update([
                'email' => 'email' . $user->id
            ]);
        }


        if (request('branch')) {
            $user->userBranches()->sync(request('branch'));
        } else {
            $user->userBranches()->detach();
        }


        UserInfo::where('user_id', '=', $user->id)->whereIn('type', [1, 2])->delete();
        if (request('telephone')) {
            foreach (request('telephone') as $key => $value) {
                UserInfo::create([
                    'user_id' => $user->id,
                    'value'   => $value,
                    'type'    => 1
                ]);
            }
        }

        if (request('cellphone')) {
            foreach (request('cellphone') as $key => $value) {
                UserInfo::create([
                    'user_id' => $user->id,
                    'value'   => $value,
                    'type'    => 2
                ]);
            }
        }

        if (request('secondary_email')) {
            UserInfo::where('user_id', '=', $user->id)->where('type', '=', '3')->whereNotIn('id', request('secondary_email_id'))->delete();
            foreach (request('secondary_email') as $key => $value) {
                if (isset(request('secondary_email_id')[$key]) && request('secondary_email_id') != '') {
                    $info = UserInfo::find(request('secondary_email_id')[$key]);
                    $info_inputs = [
                        'value'   => $value,
                        'primary' => 0,
                    ];
                    if ($info->value != $value) {
                        $info_inputs += [
                            'sent_mail'   => 0,
                            'is_verified' => 0
                        ];
                    }
                    if (request('primary') == $key && ($info->is_verified == 1 || $info->primary == 1)) {
                        $info_inputs['primary'] = 1;
                        unset($info_inputs['value']);
                        unset($info_inputs['sent_mail']);
                        unset($info_inputs['is_verified']);
                        $user->update([
                            'email' => $info->value
                        ]);
                        Log::info($user);
                        UserInfo::where('user_id', '=', $user->id)->where('type', '=', 3)
                            ->update([
                                'primary' => 0
                            ]);
                    }
                    UserInfo::where('id', '=', request('secondary_email_id')[$key])
                        ->update($info_inputs);
                } else {
                    $primary = 0;
                    $password = str_random(6);
                    if (request('primary') == $key) {
                        $primary = 1;
                        $user->update([
                            'email'    => $value,
                            'password' => bcrypt($password)
                        ]);
                    }
                    $info = UserInfo::create([
                        'user_id' => $user->id,
                        'value'   => $value,
                        'type'    => 3,
                        'primary' => $primary
                    ]);
                    if ($primary) {
                        Log::info('mail sent to ' . $info->value);
                        try {
                            Mail::to($info->value)->send(new ConfirmEmail($user, 'normal', $info->id, $info->value, $password));
                        } catch (\Exception $e) {
                            Log::info($e);
                        }
                        Log::info('verification mail sent to ' . $info->value . '.');
                        $info->update([
                            'sent_mail' => '1'
                        ]);
                    }
                }
            }
        }

        $role = Role::find(4);
        $user->syncRoles([$role->name]);

        $data = [];
        $data['status'] = true;
        $data['user_id'] = $user->id;
        return $data;
    }

    public function indexDatatable()
    {
        $selection = [
            'users.*',
            'roles.name as role',
            'countries.name as country_name',
            'user_status.title as status_name',
            DB::raw('(select sum(amount) from wallets where wallets.user_id=users.id and wallets.deleted_at is null) as wallet')
        ];
        $users = User::select($selection)
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country')
            ->leftJoin('user_status', 'user_status.id', '=', 'users.status')
            ->whereNotIn('users.id', [auth()->user()->id, 1])
            ->groupBy('users.id');

        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $users->where('users.country', '=', $country);
            }
        } else if (auth()->user()->hasRole('admin')) {
            $users->where('users.country', '=', auth()->user()->country);
        } else {
            $users->where('users.country', '=', auth()->user()->country);
        }

        $users->where('users.role_id', '=', 4);

        return DataTables::of($users)
            ->addColumn('username', function ($row) {
                return ucwords(strtolower($row->lastname . " " . $row->firstname));
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
                    $html .= "<a href='" . url()->route('admin1.merchants.edit', $row->id) . "' class='$iconClass'
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
            ->make();
    }

    public function destroy(User $user)
    {
        $data = [];
        $data['status'] = $user->delete();
        return $data;
    }
}
