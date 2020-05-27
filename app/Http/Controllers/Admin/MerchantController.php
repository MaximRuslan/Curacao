<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\EmailHelper;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\User;
use App\Models\UserBank;
use App\Models\UserInfo;
use App\Models\UserReference;
use App\Models\UserStatus;
use App\Models\UserTerritory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin');
    }

    public function index()
    {
        $data = [];
        return view('admin.merchants.index', $data);
    }

    public function indexDatatable()
    {
        $filterUsers = [auth()->user()->id, '1'];
        $user = User::with('role')->select('users.*');

        if (auth()->user()->hasRole('super admin')) {
            $user->whereNotIn('id', $filterUsers);
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $user->where(['country' => $country]);
            }
        }
        if (request('user_id')) {
            $user->where('id', '=', request('user_id'));
        }
        $user->where('role_id', '=', 4);

        return DataTables::of($user)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a title='Edit' href='" . url()->route('merchants.edit', $data->id) . "' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a title='Delete' href='javascript:;' data-modal-id='deleteMerchant' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
//                $html .= "<a href='javascript:;' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }


    public function create()
    {
        $data = [];

        $country = session()->has('country') ? session()->get('country') : '';

        if (!auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', auth()->user()->country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('id', '=', auth()->user()->territory)
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('id', '=', auth()->user()->branch)
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
        $data['departments'] = UserDepartment::select(['id', 'title'])->get();
        $data['status'] = UserStatus::select(['id', 'title'])->get();
        return view('admin.merchants.create', $data);
    }

    public function edit(User $merchant)
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        if (!auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', auth()->user()->country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('id', '=', auth()->user()->territory)
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('id', '=', auth()->user()->branch)
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
        $data['departments'] = UserDepartment::select(['id', 'title'])->get();
        $data['status'] = UserStatus::select(['id', 'title'])->get();
        $data['edit_user'] = $merchant;
        return view('admin.merchants.create', $data);
    }

    public function store()
    {
        $inputs = request()->all();

        $user = DB::table('users')->where('email', '=', request('email'))
            ->first();
        if ($user != null) {
            $data = [
                'status'  => false,
                'type'    => 'email',
                'message' => 'Email is already taken'
            ];
            return $data;
        }
        $country = Country::find(request('country'));
        $length = 10;
        if ($country != null) {
            if ($country->phone_length != null) {
                $length = $country->phone_length;
            }
        }

        $this->validate(request(), User::validationRules($inputs, $length));
        if ((!request('telephone') && !request('cellphone')) || (count(request('telephone')) == 0 && count(request('cellphone')) == 0)) {
            $data = [];
            $data['status'] = false;
            $data['type'] = 'phone';
            $data['message'] = 'Minimum one telephone or cellphone required.';
            return $data;
        }

        $password = str_random(6);
        $inputs['password'] = bcrypt($password);
        $inputs['is_verified'] = 0;
        $inputs['role_id'] = 4;
        $inputs['dob'] = (request('dob') != '' ? date("Y-m-d", strtotime(request('dob'))) : null);
        $inputs['exp_date'] = (request('exp_date') != '' ? date("Y-m-d", strtotime(request('exp_date'))) : null);

        $user = User::create($inputs);
        $user->assignRole('merchant');

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
            foreach (request('secondary_email') as $key => $value) {
                UserInfo::create([
                    'user_id' => $user->id,
                    'value'   => $value,
                    'type'    => 3
                ]);
            }
        }
        try {
            EmailHelper::emailConfigChanges('user');
            Mail::send('emails.confirm-email', [
                'user'     => $user,
                'password' => $password
            ], function ($message) use ($user) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($user->email);
                $message->bcc(config('site.bcc_users'));
                $message->subject(config('mail.from.name') . ': Verify your online account.');
            });
        } catch (\Exception $e) {
            Log::info($e);
        }

        $data = [];
        $data['status'] = true;
        $data['user_id'] = $user->id;
        return $data;
    }

    public function update(User $merchant)
    {
        $inputs = request()->all();

        $custom_user = DB::table('users')
            ->where('email', '=', request('email'))
            ->where('id', '!=', $merchant->id)
            ->first();

        if ($custom_user != null) {
            $data = [
                'status'  => false,
                'message' => 'Email is already taken'
            ];
            return $data;
        }

        $country = Country::find(request('country'));
        $length = 10;
        if ($country != null) {
            if ($country->phone_length != null) {
                $length = $country->phone_length;
            }
        }

        $this->validate(request(), User::validationRules($inputs, $length, $merchant->id));
        if ((!request('telephone') && !request('cellphone')) || (count(request('telephone')) == 0 && count(request('cellphone')) == 0)) {
            $data = [];
            $data['status'] = false;
            $data['type'] = 'phone';
            $data['message'] = 'Minimum one telephone or cellphone required.';
            return $data;
        }

        $inputs['dob'] = (request('dob') != '' ? date("Y-m-d", strtotime(request('dob'))) : null);
        $inputs['exp_date'] = (request('exp_date') != '' ? date("Y-m-d", strtotime(request('exp_date'))) : null);

        if ($merchant->email != $inputs['email']) {
            $inputs['is_verified'] = 0;
            $merchant->update($inputs);
            try {
                EmailHelper::emailConfigChanges('user');
                Mail::send('emails.confirm-email', [
                    'user'     => $merchant,
                    'password' => false
                ], function ($message) use ($merchant) {
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                    $message->to($merchant->email);
                    $message->bcc(config('site.bcc_users'));
                    $message->subject(config('mail.from.name') . ': Verify your online account.');
                });
            } catch (\Exception $e) {
                Log::info($e);
            }
        } else {
            $merchant->update($inputs);
        }

        UserInfo::where('user_id', '=', $merchant->id)->delete();
        if (request('telephone')) {
            foreach (request('telephone') as $key => $value) {
                UserInfo::create([
                    'user_id' => $merchant->id,
                    'value'   => $value,
                    'type'    => 1
                ]);
            }
        }

        if (request('cellphone')) {
            foreach (request('cellphone') as $key => $value) {
                UserInfo::create([
                    'user_id' => $merchant->id,
                    'value'   => $value,
                    'type'    => 2
                ]);
            }
        }

        if (request('secondary_email')) {
            foreach (request('secondary_email') as $key => $value) {
                UserInfo::create([
                    'user_id' => $merchant->id,
                    'value'   => $value,
                    'type'    => 3
                ]);
            }
        }

        $data = [];
        $data['user_id'] = $merchant->id;
        $data['status'] = true;
        return $data;
    }

    public function show($id)
    {
        $user = User::find($id);
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
            'email'            => ["type" => "text", 'value' => $user->email],
            'mobile_no'        => ["type" => "text", 'value' => $user->mobile_no],
            'territory'        => ["type" => "select", 'value' => $user->territory],
            'branch'           => ["type" => "select", 'value' => $user->branch],
            'status'           => ["type" => "select", 'value' => $user->status],
            'address'          => ["type" => 'textarea', 'value' => $user->address],
            'country'          => [
                "type"      => 'select-territory',
                'value'     => $user->country,
                'territory' => $user->territory,
                'branch'    => $user->branch
            ],
            'country_code'     => [
                'type'  => 'text',
                'value' => $country_code
            ],
            'phone_length'     => [
                'type'  => 'text',
                'value' => $phone_length
            ],
            'contact_person'   => ["type" => 'text', 'value' => $user->contact_person],
            'transaction_type' => ['type' => 'select', 'value' => $user->transaction_type],
            'transaction_fee'  => ['type' => 'text', 'value' => $user->transaction_fee],
            'commission_type'  => ['type' => 'select', 'value' => $user->commission_type],
            'commission_fee'   => ['type' => 'text', 'value' => $user->commission_fee],
        ];
        $telephones = UserInfo::where('user_id', '=', $id)->where('type', '=', 1)->pluck('value');
        $cellphones = UserInfo::where('user_id', '=', $id)->where('type', '=', 2)->pluck('value');
        $emails = UserInfo::where('user_id', '=', $id)->where('type', '=', 3)->pluck('value');
        return response()->json([
            "status"     => "success",
            "inputs"     => $filteredArr,
            'telephones' => $telephones,
            'cellphones' => $cellphones,
            'emails'     => $emails
        ]);
    }

    public function destroy($id)
    {
        //
        $user = User::find($id);
        $user->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    public function ajaxBanksGet(User $user)
    {
        $data = [];
        $data['banks_data'] = Bank::select('name', 'id')
            ->where('country_id', '=', $user->country)
            ->pluck('name', 'id');
        $data['banks'] = UserBank::where('user_id', '=', $user->id)->get();
        return $data;
    }

    public function ajaxUserTerritoryBanks()
    {
        $data = [];
        $user = User::find(request('user_id'));
        $data['banks'] = Bank::select('name', 'id')
            ->where('country_id', '=', $user->country)
            ->pluck('name', 'id');
        return $data;
    }

    public function ajaxBanksUpdate(user $user)
    {
        $data = [];
        $validator = Validator::make(request()->all(), UserBank::validationRules(), UserBank::validationMessage());
        if ($validator->fails()) {
            $data['status'] = false;
            $data['inputs'] = request()->all();
            $data['banks'] = Bank::where('territory_id', '=', $user->territory)
                ->pluck('name', 'id');
            $data['errors'] = $validator->errors();
        } else {
            UserBank::where('user_id', '=', $user->id)->delete();
            if (request('account_number')) {
                foreach (request('account_number') as $key => $value) {
                    UserBank::create([
                        'user_id'            => $user->id,
                        'account_number'     => $value,
                        'bank_id'            => request('bank_id')[$key],
                        'name_on_account'    => request('name_on_account')[$key],
                        'address_on_account' => request('address_on_account')[$key],
                    ]);
                }
            }
            $data['status'] = true;
        }
        return $data;
    }

    public function ajaxReferencesGet(User $user)
    {
        $data = [];
        $data['references'] = UserReference::where('user_id', '=', $user->id)->get();
        return $data;
    }

    public function ajaxReferencesUpdate(user $user)
    {
        $data = [];
        $validator = Validator::make(request()->all(), UserReference::validationRules(), UserReference::validationMessage());
        if ($validator->fails()) {
            $data['status'] = false;
            $data['inputs'] = request()->all();
            $data['errors'] = $validator->errors();
        } else {
            UserReference::where('user_id', '=', $user->id)->delete();
            if (request('first_name')) {
                foreach (request('first_name') as $key => $value) {
                    UserReference::create([
                        'user_id'      => $user->id,
                        'first_name'   => $value,
                        'last_name'    => request('last_name')[$key],
                        'relationship' => request('relationship')[$key],
                        'telephone'    => request('telephone')[$key],
                        'cellphone'    => request('cellphone')[$key],
                        'address'      => request('address')[$key],
                    ]);
                }
            }
            $data['status'] = true;
        }
        return $data;
    }
}
