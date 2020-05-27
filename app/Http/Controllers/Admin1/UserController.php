<?php

namespace App\Http\Controllers\Admin1;

use App\Http\Controllers\Controller;
use App\Library\EmailHelper;
use App\Library\Helper;
use App\Library\TemplateHelper;
use App\Mail\ConfirmEmail;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Documents;
use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanNotes;
use App\Models\LoanStatus;
use App\Models\LoanType;
use App\Models\ReferralHistory;
use App\Models\Relationship;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBank;
use App\Models\UserInfo;
use App\Models\UserReference;
use App\Models\UserStatus;
use App\Models\UserTerritory;
use App\Models\UserWork;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:super admin|admin|processor|auditor|debt collector|loan approval|credit and processing')->only([
            'index',
            'webRegistrations',
            'infoShow',
            'indexDatatable',
            'worksInfo',
            'WordsEdit',
            'userBanks',
            'userReferences',
        ]);
        $this->middleware('role:super admin|admin|processor')->only([
            'create',
            'edit',
            'destroy',
            'show',
            'store',
            'countryData',
            'documentDelete',
            'workStore',
            'worksDelete',
            'workingTypeStore',
            'userBanksStore',
            'userCountryBanks',
            'userReferencesStore',
            'walletDatatable',
            'walletStore',
            'countryPdf',
        ]);
        $this->middleware('role:super admin|admin')->only([
            'getUsersExcel',
        ]);
    }

    public function index()
    {
        return view('admin1.pages.users.index');
    }

    public function webRegistrations()
    {
        $data = [];
        $data['type'] = 'web';
        return view('admin1.pages.users.index', $data);
    }

    public function create()
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        $data['lang'] = config('site.language');

        $data['roles'] = Role::select('*')
            ->where('id', '!=', '4');
        if (auth()->user()->hasRole('admin')) {
            $data['roles']->whereNotIn('name', ['super admin', 'admin']);
        } else {
            if (auth()->user()->hasRole('processor')) {
                $data['roles']->where('name', '=', 'client');
            }
        }
        $data['roles'] = $data['roles']
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');
        $data['roles'] = $data['roles']->map(function ($item, $key) {
            return ucwords(strtolower($item));
        });

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
        } else {
            if (auth()->user()->hasRole('super admin') && $country != '') {
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

        $data['civil_statues'] = User::getCivilStatuses();

        $data['options'] = [
            '1' => Lang::get('keywords.yes'),
            '2' => Lang::get('keywords.no'),
        ];

        return view('admin1.pages.users.create', $data);
    }

    public function edit(User $user)
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        $data['lang'] = config('site.language');

        $data['roles'] = Role::select('*')
            ->where('id', '!=', '4');
        if (auth()->user()->hasRole('admin')) {
            $data['roles']->whereNotIn('name', ['super admin', 'admin']);
        } else {
            if (auth()->user()->hasRole('processor')) {
                $data['roles']->where('name', '=', 'client');
            }
        }
        $data['roles'] = $data['roles']
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');
        $data['roles'] = $data['roles']->map(function ($item, $key) {
            return ucwords(strtolower($item));
        });

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
        } else {
            if (auth()->user()->hasRole('super admin') && $country != '') {
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
        $data['relationships'] = Relationship::pluck('title', 'id');

        $data['user'] = $user;

        $data['civil_statues'] = User::getCivilStatuses();

        $data['options'] = [
            '1' => Lang::get('keywords.yes'),
            '2' => Lang::get('keywords.no'),
        ];

        $referred_by = User::where('referral_code', '=', $user->referred_by)->whereNotNull('referral_code')->first();
        if ($referred_by != null) {
            $referred_by = '<a href="' . route('admin1.users.show', $referred_by->id) . '">' . $referred_by->firstname . ' ' . $referred_by->lastname . '</a>';
        }
        $data['referred_by'] = $referred_by;

        return view('admin1.pages.users.create', $data);
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

        $referred_by = User::where('referral_code', '=', $user->referred_by)->whereNotNull('referral_code')->first();
        if ($referred_by != null) {
            $referred_by = '<a href="' . route('admin1.users.show', $referred_by->id) . '">' . $referred_by->firstname . ' ' . $referred_by->lastname . '</a>';
        }
        $filteredArr = [
            'id'                => ["type" => "hidden", 'value' => $user->id],
            'firstname'         => ["type" => "text", 'value' => $user->firstname],
            'lastname'          => ["type" => "text", 'value' => $user->lastname],
            'email'             => ["type" => "email", 'value' => $user->email],
            'address'           => ["type" => "textarea", 'value' => $user->address],
            'lang'              => ["type" => "select2", 'value' => $user->lang],
            'role_id'           => ["type" => "select2", 'value' => $user->role_id],
            'department'        => ["type" => "select2", 'value' => $user->department],
            'status'            => ["type" => "select2", 'value' => $user->status],
            'sex'               => ["type" => "radio", 'value' => $user->sex],
            'profile_pic'       => ["type" => "file", 'value' => $user->profile_pic],
            'id_number'         => ["type" => 'text', 'value' => $user->id_number],
            'dob'               => ["type" => 'text', 'value' => $user->dob],
            'place_of_birth'    => ["type" => 'text', 'value' => $user->place_of_birth],
            'country'           => ["type" => 'select2', 'value' => $user->country],
            'territory'         => ['type' => 'select2', 'value' => $user->territory],
            'branches'          => ['type' => 'select2', 'value' => $user->userBranches->pluck('id')],
            'country_code'      => ['type' => 'text', 'value' => $country_code],
            'phone_length'      => ['type' => 'text', 'value' => $phone_length],
            'civil_status'      => ["type" => 'select2', 'value' => $user->civil_status],
            'spouse_first_name' => ["type" => 'text', 'value' => $user->spouse_first_name],
            'spouse_last_name'  => ["type" => 'text', 'value' => $user->spouse_last_name],
            'exp_date'          => ["type" => 'text', 'value' => $user->exp_date],
            'pp_number'         => ["type" => 'text', 'value' => $user->pp_number],
            'pp_exp_date'       => ["type" => 'text', 'value' => $user->pp_exp_date],
            'scan_id'           => ["type" => 'file', 'value' => $user->scan_id],
            'address_proof'     => ["type" => 'file', 'value' => $user->address_proof],
            'payslip1'          => ["type" => 'file', 'value' => $user->payslip1],
            'payslip2'          => ["type" => 'file', 'value' => $user->payslip2],
            'other_document'    => ["type" => 'file', 'value' => $user->other_document],
            'referred_by'       => ["type" => 'text', 'value' => $referred_by],
            'complete_profile'  => ['type' => 'boolean', 'value' => $user->complete_profile],
            'commission'        => ['type' => 'number', 'value' => $user->commission],
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
            'emails'          => $emails,
        ]);
    }

    public function infoShow(User $user)
    {
        $data = [];
        $user = User::select('users.*', 'user_status.title as status', 'countries.name as country',
            'user_territories.title as territory', 'countries.country_code as country_code', 'countries.referral as country_referral',
            DB::raw('group_concat(branches.title) as branch'))
            ->leftJoin('user_status', 'user_status.id', '=', 'users.status')
            ->leftJoin('countries', 'countries.id', '=', 'users.country')
            ->leftJoin('user_territories', 'user_territories.id', '=', 'users.territory')
            ->leftJoin('user_branches', 'user_branches.user_id', '=', 'users.id')
            ->leftJoin('branches', 'branches.id', '=', 'user_branches.branch_id')
            ->groupBy('users.id')
            ->where('users.id', '=', $user->id)
            ->first();

        $documents = Documents::select('name', 'id', 'document')
            ->where('main_id', '=', $user->id)
            ->where('type', '=', '1')
            ->get();
        foreach ($documents as $key => $value) {
            $value->document = asset('uploads/' . $value->document);
        }

        $user->other_documents = $documents;


        $user_infos = UserInfo::select(DB::raw('group_concat(value) as value'), 'type')
            ->where('user_id', '=', $user->id)
            ->groupBy('type')
            ->pluck('value', 'type');
        $user['telephones'] = '';
        $user['cellphones'] = '';
        $user['extra_emails'] = '';
        if (isset($user_infos['1'])) {
            $inputs = explode(',', $user_infos['1']);
            foreach ($inputs as $key => $value) {
                $inputs[$key] = $user->country_code . $value;
            }
            $user['telephones'] = implode(', ', $inputs);
        }
        if (isset($user_infos['2'])) {
            $inputs = explode(',', $user_infos['2']);
            foreach ($inputs as $key => $value) {
                $inputs[$key] = $user->country_code . $value;
            }
            $user['cellphones'] = implode(', ', $inputs);
        }
        if (isset($user_infos['3'])) {
            $inputs = explode(',', $user_infos['3']);
            $user['extra_emails'] = implode("<br>", $inputs);
        }

        $data['user'] = $user;
        $data['relationships'] = Relationship::pluck('title', 'id');
        $referred_by_name = '';
        if ($user->referred_by != null) {
            $referred_by = User::where('referral_code', '=', $user->referred_by)->first();
            $referred_by_name = '<a href="' . route('admin1.users.show', $referred_by->id) . '">' . $referred_by->firstname . ' ' . $referred_by->lastname . '</a>';
        }

        $data['referral_infos'] = [
            [
                'title' => 'Code',
                'value' => $user->referral_code,
            ],
            [],
            [
                'title' => 'Total current referrals',
                'value' => $user->getStatusReferrals(4),
            ],
            [
                'title' => 'Total in default referrals',
                'value' => $user->getStatusReferrals(5),
            ],
            [
                'title' => 'Total debt collector referrals',
                'value' => $user->getStatusReferrals(6),
            ],
            [
                'title' => 'Total referrals',
                'value' => $user->getStatusReferrals(),
            ],
            [],
            [
                'title' => 'Referred by',
                'value' => $referred_by_name,
            ],
        ];

        if (request('json') == 'data') {
            return $data;
        }
        return view('admin1.pages.users.view', $data);
    }

    public function store()
    {
        $inputs = request()->all();
        $format = config('site.date_format.php');

        $this->validate(request(), User::userValidationRules($inputs), ['primary.required' => "Minimum one primary email required."]);

        if (request('id') != '') {
            $user = User::find(request('id'));
        }

        $country = null;
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $country = Country::find(session('country'));
            } else {
                $country = Country::find(request('country'));
            }
        } else {
            $country = Country::find(auth()->user()->country);
        }
        $inputs['country'] = $country->id;

        if (isset($inputs['referred_by']) && $inputs['referred_by'] != null && $inputs['referred_by'] != '') {
            $refferal_user = User::where('referral_code', '=', $inputs['referred_by'])->first();
            if ($refferal_user == null || $refferal_user->role_id != 3 || $refferal_user->country != $inputs['country']) {
                $data = [];
                $data['status'] = false;
                $data['type'] = 'referred_by';
                $data['message'] = 'Please enter correct input for referral code.';
                return $data;
            }
        }
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
        if (request()->hasFile('profile_pic')) {
            if (isset($user) && $user != null && $user->profile_pic != '') {
                Storage::delete(public_path('uploads/' . $user->profile_pic));
            }
            $profile = time() . '_' . request()->file('profile_pic')->getClientOriginalName();
            $path = request()->profile_pic->move(public_path('uploads'), $profile);
            $inputs['profile_pic'] = $profile;
        }

        if (request()->hasFile('scan_id')) {
            if (isset($user) && $user != null && $user->scan_id != '') {
                Storage::delete(public_path('uploads/' . $user->scan_id));
            }
            $profile = time() . '_' . request()->file('scan_id')->getClientOriginalName();
            $path = request()->scan_id->move(public_path('uploads'), $profile);
            $inputs['scan_id'] = $profile;
        }

        if (request()->hasFile('address_proof')) {
            if (isset($user) && $user != null && $user->address_proof != '') {
                Storage::delete(public_path('uploads/' . $user->address_proof));
            }
            $proof = time() . '_' . request()->file('address_proof')->getClientOriginalName();
            $path = request()->address_proof->move(public_path('uploads'), $proof);
            $inputs['address_proof'] = $proof;
        }

        if (request()->hasFile('payslip1')) {
            if (isset($user) && $user != null && $user->payslip1 != '') {
                Storage::delete(public_path('uploads/' . $user->payslip1));
            }
            $payslip1 = time() . '_' . request()->file('payslip1')->getClientOriginalName();
            $path = request()->payslip1->move(public_path('uploads'), $payslip1);
            $inputs['payslip1'] = $payslip1;
        }

        if (request()->hasFile('payslip2')) {
            if (isset($user) && $user != null && $user->payslip2 != '') {
                Storage::delete(public_path('uploads/' . $user->payslip2));
            }
            $payslip2 = time() . '_' . request()->file('payslip2')->getClientOriginalName();
            $path = request()->payslip2->move(public_path('uploads'), $payslip2);
            $inputs['payslip2'] = $payslip2;
        }

        if (request('dob')) {
            $date = \DateTime::createFromFormat($format, request('dob'));
            $inputs['dob'] = $date->format('Y-m-d');
        } else {
            $inputs['dob'] = null;
        }
        if (request('exp_date')) {
            $date = \DateTime::createFromFormat($format, request('exp_date'));
            $inputs['exp_date'] = $date->format('Y-m-d');
        } else {
            $inputs['exp_date'] = null;
        }

        if (request('pp_exp_date')) {
            $date = \DateTime::createFromFormat($format, request('pp_exp_date'));
            $inputs['pp_exp_date'] = $date->format('Y-m-d');
        } else {
            $inputs['pp_exp_date'] = null;
        }

        if ($inputs['role_id'] != '3') {
            $inputs['lang'] = 'eng';
        }

        if (isset($user) && $user != null) {
            $user->update($inputs);
            if ($user->referral_code == null) {
                $user->update([
                    'referral_code' => $user->getReferralCode(),
                ]);
            }
        } else {
            if ($inputs['role_id'] != '3') {
                $inputs['complete_profile'] = 1;
            }
            $inputs['web_registered'] = 1;
            $inputs['password'] = '';
            $inputs['email'] = '';
            $inputs['is_verified'] = 0;
            $user = User::create($inputs);
            $user->update([
                'email'         => 'email' . $user->id,
                'referral_code' => $user->getReferralCode(),
            ]);
        }


        if (request('branch')) {
            $user->userBranches()->sync(request('branch'));
        }


        if (request('other_document_id')) {
            foreach (request('other_document_id') as $key => $value) {
                if ($value != '') {
                    $document = Documents::where('id', '=', request('other_document_id')[$key])
                        ->where('main_id', '=', $user->id)
                        ->where('type', '=', '1')
                        ->update([
                            'name' => request('other_old_document_name')[$key],
                        ]);
                }
            }
        }

        if (request('other_document')) {
            foreach (request('other_document') as $key => $value) {
                $doc = time() . '_' . $value->getClientOriginalName();
                $path = $value->move(public_path('uploads'), $doc);
                Documents::create([
                    'main_id'  => $user->id,
                    'type'     => '1',
                    'name'     => request('other_document_name')[$key],
                    'document' => $doc,
                ]);
            }
        }

        UserInfo::where('user_id', '=', $user->id)->whereIn('type', [1, 2])->update([
            'deleted_by' => auth()->user()->id,
        ]);

        UserInfo::where('user_id', '=', $user->id)->whereIn('type', [1, 2])->delete();

        if (request('telephone')) {
            foreach (request('telephone') as $key => $value) {
                UserInfo::create([
                    'user_id' => $user->id,
                    'value'   => $value,
                    'type'    => 1,
                ]);
            }
        }

        if (request('cellphone')) {
            foreach (request('cellphone') as $key => $value) {
                UserInfo::create([
                    'user_id' => $user->id,
                    'value'   => $value,
                    'type'    => 2,
                ]);
            }
        }

        if (request('secondary_email')) {
            UserInfo::where('user_id', '=', $user->id)->where('type', '=', '3')->whereNotIn('id', request('secondary_email_id'))->delete();
            foreach (request('secondary_email') as $key => $value) {
                $info = null;
                if (isset(request('secondary_email_id')[$key]) && request('secondary_email_id') != '') {
                    $info = UserInfo::find(request('secondary_email_id')[$key]);
                    $info_inputs = [
                        'value'   => $value,
                        'primary' => 0,
                    ];
                    if ($info->value != $value) {
                        $info_inputs += [
                            'sent_mail'   => 0,
                            'is_verified' => 0,
                        ];
                    }
                    if (request('primary') == $key && ($info->is_verified == 1 || $info->primary == 1)) {
                        $info_inputs['primary'] = 1;
                        unset($info_inputs['value']);
                        unset($info_inputs['sent_mail']);
                        unset($info_inputs['is_verified']);
                        $user->update([
                            'email' => $info->value,
                        ]);
                        UserInfo::where('user_id', '=', $user->id)->where('type', '=', 3)->update([
                            'primary' => 0,
                        ]);
                    }
                    UserInfo::where('id', '=', request('secondary_email_id')[$key])->update($info_inputs);
                    $info = UserInfo::find(request('secondary_email_id')[$key]);
                } else {
                    $primary = 0;
                    $password = $user->createPassword();
                    if (request('primary') == $key) {
                        $primary = 1;
                        $user->update([
                            'email'    => $value,
                            'password' => bcrypt($password),
                        ]);
                    }
                    $info = UserInfo::create([
                        'user_id' => $user->id,
                        'value'   => $value,
                        'type'    => 3,
                        'primary' => $primary,
                    ]);
                    if ($primary && $user->complete_profile == 1) {
                        Log::info('mail sent to ' . $info->value);
                        try {
                            Mail::to($info->value)->send(new ConfirmEmail($user, 'normal', $info->id, $info->value, $password));
                        } catch (\Exception $e) {
                            Log::info($e);
                        }
                        Log::info('verification mail sent to ' . $info->value . '.');
                        $info->update([
                            'sent_mail' => '1',
                        ]);
                    }
                }
            }
        }

        $role = Role::find($user->role_id);
        $user->syncRoles([$role->name]);

        $data = [];
        $data['status'] = true;
        $data['user_id'] = $user->id;
        $data['role_id'] = $user->role_id;
        return $data;
    }

    public function indexDatatable()
    {
        $statuses = UserStatus::select('id', 'title', 'role')
            ->orderBy('title', 'asc')
            ->get();
        $statuses = $statuses->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        });
        $selection = [
            'users.*',
            'roles.name as role',
            'countries.name as country_name',
            'countries.pagare',
            'user_status.title as status_name',
            DB::raw('(select sum(amount) from wallets where wallets.user_id=users.id and wallets.deleted_at is null) as wallet'),
            DB::raw('(select loan_applications.id from loan_applications where loan_applications.loan_status in (' . implode(',',
                    LoanApplication::inActiveStatuses()) . ') and loan_applications.client_id = users.id and loan_applications.deleted_at is null limit 1) as loan_id'),
            DB::raw('(select loan_applications.employee_id from loan_applications where loan_applications.loan_status in (' . implode(',',
                    LoanApplication::inActiveStatuses()) . ') and loan_applications.client_id = users.id and loan_applications.deleted_at is null limit 1) as employee_id'),

        ];
        $users = User::select($selection)
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country')
            ->leftJoin('user_status', 'user_status.id', '=', 'users.status')
            ->whereNotIn('users.id', [auth()->user()->id, 1]);

        if (request('status')) {
            $users->where('users.status', '=', request('status'));
        }
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $users->where('users.country', '=', $country);
            }
        } else {
            $country = auth()->user()->country;
            if (auth()->user()->hasRole('admin')) {
                $users->whereNotIn('users.role_id', ['1', '5'])
                    ->where('users.country', '=', auth()->user()->country);
            } else {
                $users->where('users.role_id', '=', '3')
                    ->where('users.country', '=', auth()->user()->country);
            }
        }

        if (request('role')) {
            $users->where('users.role_id', '=', request('role'));
        } else {
            $users->where('users.role_id', '!=', 4);
        }

        if (request('type') && request('type') == 'web') {
            $users->whereNull('users.web_registered');
        } else {
            $users->where('users.web_registered', '=', 1);
        }
        $employee = User::getEmployees($country);
        return DataTables::of($users)
            ->addColumn('collector_first_name', function ($row) use ($employee) {
                if ($row['employee_id'] != null) {
                    $options = '';
                    $employee_name = '';
                    foreach ($employee as $key => $value) {
                        $selected = '';
                        if ($key == $row['employee_id']) {
                            $selected = '<i class="fa fa-check"></i>';
                            $employee_name = $value;
                        }
                        $options .= '<a class="dropdown-item js--employee-change" href="javascript;" data-id="' . $row['loan_id'] . '" data-user-id="' . $key . '">' . $selected . $value . '</a>';
                    }
                    return '<div class="btn-group">
                            <a href="javascript;" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">
                                ' . $employee_name . '
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1" style="height: 200px;overflow-y: auto;overflow-x: hidden;">
                                ' . $options . '
                            </div>
                        </div>';
                }
            })
            ->addColumn('username', function ($row) {
                return ucwords(strtolower($row->lastname . " " . $row->firstname));
            })
            ->addColumn('wallet', function ($row) {
                if ($row->wallet == null) {
                    return Helper::decimalShowing("0.00", $row->country);
                }
                return Helper::decimalShowing($row->wallet, $row->country);
            })
            ->addColumn('date_time', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->addColumn('is_verified', function ($row) {
                return $row->is_verified == 1 ? 'Yes' : 'No';
            })
            ->addColumn('action', function ($row) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';

                if (($row->role_id == 3 && auth()->user()->hasRole('admin|processor')) || auth()->user()->hasRole('super admin')) {
                    $html .= "<a href='" . url()->route('admin1.users.edit', $row->id) . "' class='$iconClass'
                                data-toggle='tooltip' title='Edit'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                }

                $html .= "<a href='" . url()->route('admin1.users.show', $row->id) . "' class='$iconClass ViewInfo'
                                data-toggle='tooltip' title='View Info'>
                            <i class='fa fa-eye'></i>
                        </a>";

                //$html .= "<a href='javascript:;' data-modal-id='deleteUser' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                if ($row->role_id == 3) {
                    $html .= "<a href='javascript:;' data-id='$row->id' class='$iconClass AddAmount' 
                                    data-name='" . ucwords(strtolower($row->lastname . ' ' . $row->firstname)) . "' 
                                    data-client_id='" . $row->id_number . "'
                                    data-balance='" . Helper::decimalShowing($row->wallet, $row->country) . "' data-toggle='tooltip' title='Wallet'>
                                <i class='fa fa-google-wallet'></i>
                            </a>";
                }

                if ($row->signature != null && $row->role_id == 3) {
                    $html .= '<a href="' . asset('uploads/' . $row->signature) . '" target="_blank" download="" 
                                    class="btn btn-sm waves-effect btn-info" data-toggle="tooltip" title="Signature">
                                <i class="fa fa-paperclip"></i>
                              </a>';
                }

                if ($row->pagare == 1 && $row->role_id == 3 && auth()->user()->hasRole('super admin|admin|processor')) {
                    $html .= '<a href="#nogo" class="btn btn-sm waves-effect btn-info downloadCountryPdf" data-id="' . $row->id . '" data-toggle="tooltip" title="PAGARE">
                            <i class="fa fa-file-pdf-o"></i>
                          </a>';
                }
                if ($row->web_registered != '1' && (($row->role_id == 3 && auth()->user()->hasRole('admin|processor')) || auth()->user()->hasRole('super admin'))) {
                    $html .= "<a href='#nogo' data-id='$row->id' class='$iconClass btn-danger deleteUser' data-toggle='tooltip' title='Delete'>
                            <i class='fa fa-trash'></i>
                        </a>";
                }

                $html .= "</div>";

                return $html;
            })
            ->with(['statuses' => $statuses])
            ->rawColumns(['collector_first_name', 'action'])
            ->make();
    }

    public function destroy(User $user)
    {
        $data = [];
        $data['status'] = false;
        if ($user->web_registered != '1') {
            $data['status'] = $user->deleteEmail();
        }
        return $data;
    }

    public function countryData(Country $country)
    {
        $data = [];
        $data['country_code'] = $country->country_code;
        $data['phone_length'] = $country->phone_length;
        $data['branches'] = Branch::where('country_id', '=', $country->id)->pluck('title', 'id');
        $data['districts'] = UserTerritory::where('country_id', '=', $country->id)->pluck('title', 'id');
        return $data;
    }

    public function documentDelete(Documents $document)
    {
        $data = [];
        Storage::delete(public_path('uploads/' . $document->document));
        $data['status'] = $document->delete();
        return $data;
    }

    public function worksInfo(User $user)
    {
        $data = [];
        $data['working_type'] = $user->working_type;
        $format = config('site.date_format.php');
        $data['works'] = UserWork::where('user_id', '=', $user->id)->get();
        foreach ($data['works'] as $key => $value) {
            if ($value->employed_since) {
                $value->employed_since = date($format, strtotime($value->employed_since));
            }
            if ($value->contract_expires) {
                $value->contract_expires = date($format, strtotime($value->contract_expires));
            } else {
                $value->contract_expires = '';
            }
            $value->payment_frequency = config('site.payment_frequency')[$value->payment_frequency];
            $value->employment_type = config('site.employment_type')[$value->employment_type];
        }
        return $data;
    }

    public function workStore(User $user)
    {
        $this->validate(request(), UserWork::validationRules());
        $data = [];
        $format = config('site.date_format.php');
        if (request('id') && request('id') != '' && request('id') != '0') {
            $work = UserWork::where('user_id', '=', $user->id)
                ->where('id', '=', request('id'))
                ->first();
            $work->fill(request()->all());
        } else {
            $work = new UserWork(request()->all());
            $work->user_id = $user->id;
        }
        if ($work != null) {
            if (request('employed_since')) {
                $date = \DateTime::createFromFormat($format, request('employed_since'));
                $work->employed_since = $date->format('Y-m-d');
            }
            if (request('contract_expires')) {
                $date = \DateTime::createFromFormat($format, request('contract_expires'));
                $work->contract_expires = $date->format('Y-m-d');
            }
            if ($work->contract_expires != '' && ($work->employed_since > $work->contract_expires)) {
                $errors = [
                    'contract_expires' => ["Contract expires should be higher than employed since."],
                ];
                return response($errors, 422);
            } else {
                if (!$work->save()) {
                    $data['status'] = false;
                } else {
                    $data['status'] = true;
                }
            }
        } else {
            $data['status'] = false;
        }
        return $data;
    }

    public function worksEdit(User $user, UserWork $work)
    {
        $data = [];
        $format = config('site.date_format.php');
        if ($work->user_id == $user->id) {
            if ($work->employed_since != null) {
                $work->employed_since = date($format, strtotime($work->employed_since));
            }
            if ($work->contract_expires != null) {
                $work->contract_expires = date($format, strtotime($work->contract_expires));
            }
            $data['work'] = [
                'id'                        => ["type" => "hidden", 'value' => $work->id],
                'employer'                  => ["type" => "text", 'value' => $work->employer],
                'address'                   => ["type" => "textarea", 'value' => $work->address],
                'telephone_code'            => ["type" => "number", 'value' => $work->telephone_code],
                'telephone'                 => ["type" => "number", 'value' => $work->telephone],
                'extension'                 => ["type" => "text", 'value' => $work->extension],
                'position'                  => ["type" => "text", 'value' => $work->position],
                'employed_since'            => ["type" => "text", 'value' => $work->employed_since],
                'employment_type'           => ["type" => "select2", 'value' => $work->employment_type],
                'department'                => ["type" => "text", 'value' => $work->department],
                'supervisor_name'           => ["type" => "text", 'value' => $work->supervisor_name],
                'supervisor_telephone'      => ["type" => "number", 'value' => $work->supervisor_telephone],
                'supervisor_telephone_code' => ["type" => "number", 'value' => $work->supervisor_telephone_code],
                'contract_expires'          => ["type" => "text", 'value' => $work->contract_expires],
                'salary'                    => ["type" => "number", 'value' => $work->salary],
                'payment_frequency'         => ["type" => "select2", 'value' => $work->payment_frequency],
            ];
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        return $data;
    }

    public function worksDelete(User $user, UserWork $work)
    {
        $data = [];
        if ($work->user_id == $user->id) {
            $work->update([
                'deleted_by' => auth()->user()->id,
            ]);
            $data['status'] = $work->delete();
        } else {
            $data['status'] = false;
        }
        return $data;
    }

    public function workingTypeStore(User $user)
    {
        $data = [];
        $user->update([
            'working_type' => request('type'),
        ]);
        return $data;
    }

    public function userBanks(User $user)
    {
        $data = [];
        $data['user'] = $user;
        $data['banks_data'] = Bank::select('name', 'id')
            ->where('country_id', '=', $user->country)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $data['banks'] = UserBank::select('user_banks.*', 'banks.transaction_fee_type', 'banks.transaction_fee')
            ->leftJoin('banks', 'banks.id', '=', 'user_banks.bank_id')
            ->where('user_banks.user_id', '=', $user->id)
            ->get();

        $data['branches'] = Branch::where('country_id', '=', $user->country)->pluck('title', 'id');
        return $data;
    }

    public function userBanksStore(user $user)
    {
        $this->validate(request(), UserBank::validationRules(), UserBank::validationMessage());
        $banks = UserBank::where('user_id', '=', $user->id)->get();
        UserBank::whereIn('id', $banks->pluck('id'))->update([
            'deleted_by' => auth()->user()->id,
        ]);
        UserBank::whereIn('id', $banks->pluck('id'))->delete();

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
        $user->update(request()->only([
            'how_much_loan',
            'repay_loan_2_weeks',
            'have_bank_loan',
            'have_bank_account',
        ]));
        $data['status'] = true;
        return $data;
    }

    public function userCountryBanks(User $user)
    {
        $data = [];
        $data['banks'] = Bank::select('banks.name', 'id')
            ->where('country_id', '=', $user->country)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');
        return $data;
    }

    public function userReferences(User $user)
    {
        $data = [];
        $data['references'] = UserReference::where('user_id', '=', $user->id)->get();
        $data['relationships'] = Relationship::pluck('title', 'id');
        $data['referred_by'] = $user->referred_by;
        return $data;
    }

    public function userReferencesStore(user $user)
    {
        $data = [];
        $country = Country::find($user->country);
        $length = 10;
        if ($country != null) {
            if ($country->phone_length != null) {
                $length = $country->phone_length;
            }
        }
        $this->validate(request(), UserReference::validationRules($length), UserReference::validationMessage());

        $inputs = request()->all();

        $referred_by = null;
        if (isset($inputs['referred_by']) && $inputs['referred_by'] != null && $inputs['referred_by'] != '') {
            $referred_by = User::where('referral_code', '=', $inputs['referred_by'])->first();
            if ($referred_by == null || $referred_by->role_id != 3 || $referred_by->country != $user->country) {
                $data = [
                    'referred_by' => [
                        'Please enter correct referral code.',
                    ],
                ];
                return response()->json($data, 422);
            }
            $referred_by = $inputs['referred_by'];
        }

        $revert = false;
        $old = '';
        if ($inputs['referred_by'] != $user->referred_by) {
            $revert = true;
            $old = $user->referred_by;
        }
        $user->update([
            'referred_by' => $referred_by,
        ]);
        if ($revert) {
            if ($old != '') {
                $referred_by_old = User::where('referral_code', '=', $old)->first();
                if ($referred_by_old != null) {
                    $elements = ReferralHistory::where('client_id', '=', $referred_by_old->id)
                        ->where('referred_client', '=', $user->id)
                        ->get();
                    foreach ($elements as $key => $value) {
                        $value->delete();
                        Wallet::create([
                            'user_id'                  => $referred_by_old->id,
                            'amount'                   => '-' . $value->bonus_payout,
                            'notes'                    => 'Referral Amount revert',
                            'transaction_payment_date' => date('Y-m-d'),
                        ]);
                    }
                }
            }
            if ($referred_by != null) {
                $loans = LoanApplication::whereIn('loan_status', [4, 7])->get();
                foreach ($loans as $key => $value) {
                    ReferralHistory::storeHistory($value, 1);
                    if ($value->loan_status == 7) {
                        ReferralHistory::storeHistory($value, 2);
                    }
                }
            }
        }

        $references = UserReference::where('user_id', '=', $user->id)->get();

        UserReference::whereIn('id', $references->pluck('id'))->update([
            'deleted_by' => auth()->user()->id,
        ]);

        UserReference::whereIn('id', $references->pluck('id'))->delete();

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

        if ($user->complete_profile == 0) {
            $info = UserInfo::where('user_id', '=', $user->id)->where('primary', '=', 1)->where('type', '=', 3)->first();
            if ($info != null) {
                $password = $user->createPassword();
                $user->update([
                    'password' => bcrypt($password),
                ]);
                Log::info('mail sent to ' . $info->value);
                try {
                    Mail::to($info->value)->send(new ConfirmEmail($user, 'normal', $info->id, $info->value, $password));
                } catch (\Exception $e) {
                    Log::info($e);
                }
                Log::info('verification mail sent to ' . $info->value . '.');
                $info->update([
                    'sent_mail' => '1',
                ]);
            }
        }

        $user->update([
            'web_registered'   => 1,
            'complete_profile' => 1,
        ]);
        $data['status'] = true;
        return $data;
    }

    public function walletDatatable()
    {
        $data = Wallet::where('user_id', '=', request('user_id'));

        return DataTables::of($data)
            ->addColumn('amount', function ($data) {
                return Helper::decimalShowing($data->amount, Helper::getCountryId());
            })
            ->editColumn('type', function ($data) {
                return config('site.payment_types.' . $data->type) ? config('site.payment_types.' . $data->type) : ($data->type == 0 ? "Loan" : ($data->type == 7 ? "Credit Deduct" : ""));
            })
            ->addColumn('transaction_payment_date_format', function ($row) {
                return Helper::datebaseToFrontDate($row->transaction_payment_date);
            })
            ->addColumn('created_at_format', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->make(true);
    }

    public function walletStore(User $user)
    {
        $this->validate(request(), Wallet::validationRules());
        $data = [];
        $format = config('site.date_format.php');
        $date = \DateTime::createFromFormat($format, request('transaction_payment_date'));
        $payment_date = $date->format('Y-m-d');
        foreach (request('amount') as $key => $value) {
            if ($value != '') {
                $inputs['amount'] = $value;
                $inputs['type'] = $key;
                $inputs['user_id'] = $user->id;
                $inputs['notes'] = request('notes');
                $inputs['transaction_payment_date'] = $payment_date;
                Wallet::create($inputs);
            }
        }
        foreach (request('cashback_amount') as $key => $value) {
            if ($value != '') {
                $inputs['amount'] = '-' . $value;
                $inputs['type'] = $key;
                $inputs['user_id'] = $user->id;
                $inputs['notes'] = request('notes');
                $inputs['transaction_payment_date'] = $payment_date;
                Wallet::create($inputs);
            }
        }
        return $data;
    }

    /**
     * @desc profile related functions
     * @date 20 Jul 2018 11:06
     */

    public function profileInfo()
    {
        $data = [];
        $user = auth()->user();
        $data['user'] = [
            'firstname'   => ['type' => 'text', 'value' => $user->firstname],
            'lastname'    => ['type' => 'text', 'value' => $user->lastname],
            'email'       => ['type' => 'email', 'value' => $user->email],
            'sex'         => ['type' => 'radio', 'value' => $user->sex],
            //            'lang'        => ['type' => 'select2', 'value' => $user->lang],
            'profile_pic' => ['type' => 'image', 'value' => $user->profile_pic],
        ];
        return $data;
    }

    public function profileStore()
    {
        $data = [];
        $this->validate(request(), [
            'firstname'   => 'required|alpha',
            'lastname'    => 'required|alpha',
            //            'lang'        => 'required|in:eng,esp,pap',
            'sex'         => 'required|in:1,2',
            'profile_pic' => 'nullable|image',
        ]);

        $user = auth()->user();

        $inputs = request()->only('sex', 'firstname', 'lastname');

        if (request()->hasFile('profile_pic')) {
            if ($user->profile_pic != '') {
                Storage::delete(public_path('uploads/' . $user->profile_pic));
            }
            $profile = time() . '_' . request()->file('profile_pic')->getClientOriginalName();
            request()->profile_pic->move(public_path('uploads'), $profile);
            $inputs['profile_pic'] = $profile;
        }

        $user->update($inputs);

        //        App::setLocale($inputs['lang']);
        //
        //        session([
        //            'locale' => $inputs['lang']
        //        ]);

        $data['status'] = true;
        $data['message'] = 'User profile updated successfully.';

        return $data;
    }

    public function profilePicDelete()
    {
        $data = [];
        $user = auth()->user();
        $user->update([
            'profile_pic' => null,
        ]);
        $data['status'] = true;
        $data['message'] = 'User profile pic deleted successfully.';

        return $data;
    }

    public function changeEmail()
    {
        $this->validate(request(), ['email' => 'required|email|unique:users,email,' . auth()->user()->id]);
        $user = auth()->user();
        $user->update([
            'new_email'   => request('email'),
            'is_verified' => 0,
        ]);
        try {
            $country = Country::find($user->country);
            EmailHelper::emailConfigChanges('user');
            $email = request('email');
            Mail::send('emails.confirm-email', ['user' => $user, 'email' => $email], function ($message) use ($user, $email) {
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->to($email);
                $message->bcc(config('site.bcc_users'));
                $message->subject(config('mail.from.name') . ': Verify your online account.');
            });
        } catch (\Exception $e) {
            Log::info($e);
        }
        Auth::logout();
        $data = [];
        $data['status'] = true;
        return $data;
    }

    public function changePassword()
    {
        $this->validate(request(), [
            'old_password'     => 'required',
            'new_password'     => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
            ],
            'confirm_password' => 'required|same:new_password',
        ], [
            'new_password.regex' => 'Passwords should not be less than 8 characters including uppercase, lowercase, at least one number and special character.',
            'new_password.min'   => 'Passwords should not be less than 8 characters including uppercase, lowercase, at least one number and special character.',
        ]);
        $user = auth()->user();
        if (!Hash::check(request('old_password'), $user->password)) {
            return response()->json(['old_password' => 'The specified password does not match the database password'], 422);
        } else {
            $user->update(['password' => bcrypt(request('new_password'))]);
        }
        $data = [];
        return $data;
    }

    public function changeCountry()
    {
        $data = [];
        if (auth()->user()->hasRole('super admin')) {
            if (request('country') == 0) {
                session()->forget('country');
                session()->forget('timezone');
            } else {
                session()->put('country', request('country'));
                $country = Country::find(request('country'));
                if ($country != null) {
                    session(['timezone' => $country->timezone]);
                } else {
                    session()->forget('timezone');
                }
            }
            $data['status'] = true;
        }
        return $data;
    }

    public function getWallet($user)
    {
        $data = [];
        $data['wallet'] = Wallet::getUserWalletAmount($user);
        $data['get_hold_balance'] = User::find($user)->getHoldBalance();
        $data['available_balance'] = number_format(floatval($data['wallet']) - floatval($data['get_hold_balance']), 2);
        $data['wallet'] = number_format($data['wallet'], 2);
        return $data;
    }

    public function resendVerificationMail(User $user, UserInfo $id)
    {
        $data = [];

        Log::info('mail sent to ' . $id->sent_mail);

        $user_info = UserInfo::where('user_id', '=', $user->id)
            ->where('type', '=', 3)
            ->where('primary', '=', 1)
            ->first();

        $password = '';
        if ($user_info->is_verified == 0) {
            $password = $user->createPassword();
            $user->update([
                'password' => bcrypt($password),
            ]);
        }

        try {
            Mail::to($id->value)->send(new ConfirmEmail($user, 'normal', $id->id, $id->value, $password));
        } catch (\Exception $e) {
            Log::info($e);
        }
        Log::info('verification mail sent to ' . $id->value . '.');
        $id->update([
            'sent_mail' => '1',
        ]);

        return $data;
    }

    public function savePrimaryEmail(UserInfo $email_info)
    {
        $data = [];

        $user = User::find($email_info->id);

        if ($email_info->value != request('value')) {
            $data['status'] = $email_info->update([
                'value'       => request('value'),
                'primary'     => 0,
                'sent_mail'   => 0,
                'is_verified' => 0,
            ]);
        }

        $data['infos'] = UserInfo::where('user_id', '=', $email_info->user_id)
            ->where('type', '=', 3)
            ->get();
        $data['complete_profile'] = $user->complete_profile;

        return $data;
    }

    public function countryPdf()
    {
        $this->validate(request(), [
            'loan_type'       => 'required|numeric',
            'amount'          => 'required|numeric',
            'amount_in_words' => 'required',
            'date'            => 'required|date_format:d/m/Y',
            'id'              => 'required|numeric',
        ]);

        $user = User::find(request('id'));

        $loan_type = LoanType::find(request('loan_type'));

        $country = Country::find($user->country);

        $html = $loan_type->pagare;

        if ($html == null) {
            $html = '';
        }

        $district = UserTerritory::find($user->territory);
        if ($district != null) {
            $district = $district->title;
        } else {
            $district = '';
        }

        $position = '';

        $user_work = UserWork::where('user_id', '=', $user->id)
            ->where('employed_since', '<=', date('Y-m-d'))
            ->where('contract_expires', '>=', date('Y-m-d'))
            ->first();

        if ($user_work != null) {
            $position = $user_work->position;
        }

        $origination_fee_type = '';
        if (isset(config('site.debt_collection_fee_type')[$loan_type->origination_type])) {
            $origination_fee_type = config('site.debt_collection_fee_type')[$loan_type->origination_type];
        }
        $origination_amount = '';
        if ($loan_type->origination_amount != '' && $loan_type->origination_amount != null) {
            $origination_amount = $loan_type->origination_amount;
        }

        $renewal_type = '';
        if (isset(config('site.debt_collection_fee_type')[$loan_type->renewal_type])) {
            $renewal_type = config('site.debt_collection_fee_type')[$loan_type->renewal_type];
        }
        $renewal_amount = '';
        if ($loan_type->renewal_amount != '' && $loan_type->renewal_amount != null) {
            $renewal_amount = $loan_type->renewal_amount;
        }

        $inputs = [
            'logo'               => '<img src="' . asset('uploads/' . $country->logo) . '" style="width=100px;height:100px;">',
            'amount'             => Helper::decimalShowing(request('amount'), $country->id),
            'amount_in_words'    => request('amount_in_words'),
            'date'               => request('date'),
            'client_name'        => $user->firstname . " " . $user->lastname,
            'position_of_work'   => $position,
            'address'            => $user->address,
            'district'           => $district,
            'lang'               => $user->lang,
            'civil_status'       => Lang::get('keywords.' . config('site.civil_statues.' . $user->civil_status), [], $user->lang),
            'id_number'          => $user->id_number,
            'today_date'         => Helper::datebaseToSheetDate(date('Y-m-d H:i:s'), $country->timezone),
            'origination_type'   => $origination_fee_type,
            'origination_amount' => $origination_amount,
            'renewal_type'       => $renewal_type,
            'renewal_amount'     => $renewal_amount,
        ];

        $html = TemplateHelper::replaceNotificationTemplateTag($html, $inputs);

        $pdf = \PDF::loadHTML($html);

        $filename = time() . $user->id . 'agreement.pdf';

        $pdf->setPaper('a4', 'portrait')->save(public_path('pdf/' . $filename));

        $data = [];

        $data['url'] = asset('pdf/' . $filename);

        return $data;
    }

    public function referralStatus(User $user)
    {
        $data = [];
        if ($user->referral_status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        $user->update([
            'referral_status' => $status,
        ]);
        $data['status'] = 'success';
        if ($status == 0) {
            $data['message'] = 'Referral status successfully De-activated.';
        } else {
            if ($status == 1) {
                $data['message'] = 'Referral status successfully activated.';
            }
        }
        $data['referral_status'] = $status;
        return $data;
    }

    public function getUsersExcel()
    {
        $users = User::select('users.*')
            ->where('web_registered', '=', 1)
            ->get();
        $loans_ids = LoanApplication::select(DB::raw('min(id)'), 'client_id')->groupBy('client_id')->pluck('min(id)', 'client_id');
        $loans = LoanApplication::whereIn('id', $loans_ids->values())->get();
        $loan_statuses = LoanStatus::pluck('title', 'id');
        $countries = Country::pluck('country_code', 'id');
        $countries_names = Country::pluck('name', 'id');
        $infos = UserInfo::whereIn('user_id', $users->pluck('id'))->get();
        $user_statues = UserStatus::pluck('title', 'id');
        $data = [];
        foreach ($users as $key => $user) {
            $country_code = '';
            if ($user->country != null) {
                $country_code = $countries[$user->country];
            }
            $country_name = '';
            if ($user->country != null) {
                $country_name = $countries_names[$user->country];
            }
            $data[$key]['ID'] = $user->id;
            $data[$key]['First Name'] = $user->firstname;
            $data[$key]['Last Name'] = $user->lastname;
            $data[$key]['Email'] = $user->email;
            $data[$key]['New Email'] = $user->new_email;
            $phone_nos = $infos->where('user_id', '=', $user->id)->where('type', '=', 1)->pluck('value');
            foreach ($phone_nos as $k => $value) {
                $phone_nos[$k] = $country_code . $value;
            }
            $data[$key]['Phone Number'] = $phone_nos->implode(',');
            $mobile_nos = $infos->where('user_id', '=', $user->id)->where('type', '=', 2)->pluck('value');
            foreach ($mobile_nos as $k => $value) {
                $mobile_nos[$k] = $country_code . $value;
            }
            $data[$key]['Mobile Number'] = $mobile_nos->implode(',');
            $data[$key]['Country'] = $country_name;
            $data[$key]['Status'] = '';
            if ($user->status != null) {
                $data[$key]['Status'] = $user_statues[$user->status];
            }
            $data[$key]['Referral Code'] = $user->referral_code;
            if ($user->referral_status) {
                $data[$key]['Referral Status'] = 'Yes';
            } else {
                $data[$key]['Referral Status'] = 'No';
            }
            $loan_id = '';
            if (isset($loans_ids[$user->id])) {
                $loan_id = $loans_ids[$user->id];
            }
            $loan = null;
            if ($loan_id != '') {
                $loan = $loans->where('id', '=', $loan_id)->first();
            }
            $loan_status = '';
            if ($loan != null) {
                $loan_status = $loan_statuses[$loan->loan_status];
            }
            $data[$key]['Current Loan Status'] = $loan_status;
        }
        $filename = 'Users -' . date('Ymd') . '-' . time();
        Excel::create($filename, function ($excel) use ($data) {
            $excel->setTitle('Report OF ' . date('d-m-Y H:i:s'));
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download('xlsx');

    }

    public function loanTypes(User $user)
    {
        return LoanType::activeLoanTypesViaUserId($user)->pluck('title', 'id');
    }

    public function cockpit()
    {
        $data = [];
        $not_in = [3, 4];
        if (auth()->user()->hasAnyRole('admin')) {
            $not_in[] = 1;
        }
        $country = session()->has('country') ? session()->get('country') : '';
        $data['users'] = User::getEmployees($country);
        return view('admin1.pages.users.cockpit', $data);
    }

    public function cockpitData()
    {
        $data = [];
        //filters
        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : null;
        } else {
            $country = auth()->user()->country;
        }

        $start_date = date('Y-m-d', strtotime('first day of January')) . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 11:59:59';
        $format = config('site.date_format.php');
        if (request('start')) {
            $date = \DateTime::createFromFormat($format, request('start'));
            $start_date = $date->format('Y-m-d') . ' 00:00:00';
        }
        if (request('end')) {
            $date = \DateTime::createFromFormat($format, request('end'));
            $end_date = $date->format('Y-m-d') . ' 23:59:59';
        }

        $start_date = Helper::currentTimezoneToUtcDateTime($start_date);
        $end_date = Helper::currentTimezoneToUtcDateTime($end_date);


        //employees
        $employees = User::select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'users.id', 'users.role_id', 'roles.name as role')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $employees->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $employees->where('users.country', '=', auth()->user()->country);
            }
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $employees->whereNotIn('users.role_id', [1]);
        }
        $employees = $employees->whereNotIn('users.role_id', [3, 4]);
        if (request('user')) {
            $employees->where('users.id', '=', request('user'));
        }
        $employees = $employees->orderBy('name', 'asc')
            ->get();

        //loans getting
        $loans = LoanApplication::select('loan_applications.*')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $loans->where('users.country', '=', $country);
        } else if (!auth()->user()->hasRole('super admin')) {
            $loans->where('users.country', '=', auth()->user()->country);
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $loans->whereNotIn('users.role_id', [1]);
        }
        $loans = $loans->whereIn('loan_applications.loan_status', [4, 5, 6])->get();

        //iterations
        $iterations = LoanCalculationHistory::whereIn('loan_id', $loans->pluck('id'))
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->get();
        $all_iterations = LoanCalculationHistory::whereIn('loan_id', $loans->pluck('id'))->get();

        //notes
        $notes = LoanNotes::where('loan_id', '=', $loans->pluck('id'))->get();

        foreach ($employees as $employee) {
            $current_loans = $loans->where('loan_status', '=', 4)->where('employee_id', '=', $employee->id);
            $in_default_loans = $loans->where('loan_status', '=', 5)->where('employee_id', '=', $employee->id);
            $debt_collector_loans = $loans->where('loan_status', '=', 6)->where('employee_id', '=', $employee->id);
            $total_loans = $loans->where('employee_id', '=', $employee->id);
            $employee->debts = [
                'current'        => $current_loans->count(),
                'in_default'     => $in_default_loans->count(),
                'debt_collector' => $debt_collector_loans->count(),
                'total'          => $total_loans->count(),
            ];
            $employee->no_follow_up = [
                'current'        => $current_loans->whereNotIn('id', $notes->pluck('loan_id'))->count(),
                'in_default'     => $in_default_loans->whereNotIn('id', $notes->pluck('loan_id'))->count(),
                'debt_collector' => $debt_collector_loans->whereNotIn('id', $notes->pluck('loan_id'))->count(),
                'total'          => $total_loans->whereNotIn('id', $notes->pluck('loan_id'))->count(),
            ];
            $expired_notes = $notes->filter(function ($value, $key) {
                return $value->follow_up <= date('Y-m-d');
            });
            $employee->expired_follow_up = [
                'current'        => $current_loans->whereIn('id', $expired_notes->pluck('loan_id'))->count(),
                'in_default'     => $in_default_loans->whereIn('id', $expired_notes->pluck('loan_id'))->count(),
                'debt_collector' => $debt_collector_loans->whereIn('id', $expired_notes->pluck('loan_id'))->count(),
                'total'          => $total_loans->whereIn('id', $expired_notes->pluck('loan_id'))->count(),
            ];

            $iteration_posted = $all_iterations->where('payment_amount', '=', null);
            $iteration_collected = $iterations->where('payment_amount', '>', 0);

            $current_iterations = $iteration_posted->whereIn('loan_id', $current_loans->pluck('id'));
            $in_default_iterations = $iteration_posted->whereIn('loan_id', $in_default_loans->pluck('id'));
            $debt_collector_iterations = $iteration_posted->whereIn('loan_id', $debt_collector_loans->pluck('id'));
            $total_iterations = $iteration_posted->whereIn('loan_id', $total_loans->pluck('id'));

            $current_iterations_collected = $iteration_collected->whereIn('loan_id', $current_loans->pluck('id'));
            $in_default_iterations_collected = $iteration_collected->whereIn('loan_id', $in_default_loans->pluck('id'));
            $debt_collector_iterations_collected = $iteration_collected->whereIn('loan_id', $debt_collector_loans->pluck('id'));
            $total_iterations_collected = $iteration_collected->whereIn('loan_id', $total_loans->pluck('id'));

            $employee->principal = [
                'current'        => $current_iterations->sum('principal_posted'),
                'in_default'     => $in_default_iterations->sum('principal_posted'),
                'debt_collector' => $debt_collector_iterations->sum('principal_posted'),
                'total'          => $total_iterations->sum('principal_posted'),
            ];
            $employee->principal_collected = [
                'current'        => $current_iterations_collected->sum('principal_posted'),
                'in_default'     => $in_default_iterations_collected->sum('principal_posted'),
                'debt_collector' => $debt_collector_iterations_collected->sum('principal_posted'),
                'total'          => $total_iterations_collected->sum('principal_posted'),
            ];

            $employee->fees = [
                'current'        => $current_iterations->sum('origination_posted') + $current_iterations->sum('interest_posted') + $current_iterations->sum('renewal_posted'),
                'in_default'     => $in_default_iterations->sum('origination_posted') + $in_default_iterations->sum('interest_posted') + $in_default_iterations->sum('renewal_posted'),
                'debt_collector' => $debt_collector_iterations->sum('origination_posted') + $debt_collector_iterations->sum('interest_posted') + $debt_collector_iterations->sum('renewal_posted'),
                'total'          => $total_iterations->sum('origination_posted') + $total_iterations->sum('interest_posted') + $total_iterations->sum('renewal_posted'),
            ];
            $employee->fees_collected = [
                'current'        => $current_iterations_collected->sum('origination_posted') + $current_iterations_collected->sum('interest_posted') + $current_iterations_collected->sum('renewal_posted'),
                'in_default'     => $in_default_iterations_collected->sum('origination_posted') + $in_default_iterations_collected->sum('interest_posted') + $in_default_iterations_collected->sum('renewal_posted'),
                'debt_collector' => $debt_collector_iterations_collected->sum('origination_posted') + $debt_collector_iterations_collected->sum('interest_posted') + $debt_collector_iterations_collected->sum('renewal_posted'),
                'total'          => $total_iterations_collected->sum('origination_posted') + $total_iterations_collected->sum('interest_posted') + $total_iterations_collected->sum('renewal_posted'),
            ];

            $employee->debt = [
                'current'        => $current_iterations->sum('debt_posted') + $current_iterations->sum('debt_collection_value_posted'),
                'in_default'     => $in_default_iterations->sum('debt_posted') + $in_default_iterations->sum('debt_collection_value_posted'),
                'debt_collector' => $debt_collector_iterations->sum('debt_posted') + $debt_collector_iterations->sum('debt_collection_value_posted'),
                'total'          => $total_iterations->sum('debt_posted') + $total_iterations->sum('debt_collection_value_posted'),
            ];
            $employee->debt_collected = [
                'current'        => $current_iterations_collected->sum('debt_posted') + $current_iterations_collected->sum('debt_collection_value_posted'),
                'in_default'     => $in_default_iterations_collected->sum('debt_posted') + $in_default_iterations_collected->sum('debt_collection_value_posted'),
                'debt_collector' => $debt_collector_iterations_collected->sum('debt_posted') + $debt_collector_iterations_collected->sum('debt_collection_value_posted'),
                'total'          => $total_iterations_collected->sum('debt_posted') + $total_iterations_collected->sum('debt_collection_value_posted'),
            ];

            $employee->principal_format = [
                'current'        => Helper::decimalShowing($current_iterations->sum('principal_posted'), $country),
                'in_default'     => Helper::decimalShowing($in_default_iterations->sum('principal_posted'), $country),
                'debt_collector' => Helper::decimalShowing($debt_collector_iterations->sum('principal_posted'), $country),
                'total'          => Helper::decimalShowing($total_iterations->sum('principal_posted'), $country),
            ];
            $employee->principal_collected_format = [
                'current'        => Helper::decimalShowing($current_iterations_collected->sum('principal_posted'), $country),
                'in_default'     => Helper::decimalShowing($in_default_iterations_collected->sum('principal_posted'), $country),
                'debt_collector' => Helper::decimalShowing($debt_collector_iterations_collected->sum('principal_posted'), $country),
                'total'          => Helper::decimalShowing($total_iterations_collected->sum('principal_posted'), $country),
            ];

            $employee->fees_format = [
                'current'        => Helper::decimalShowing($current_iterations->sum('origination_posted') + $current_iterations->sum('interest_posted') + $current_iterations->sum('renewal_posted'),
                    $country),
                'in_default'     => Helper::decimalShowing($in_default_iterations->sum('origination_posted') + $in_default_iterations->sum('interest_posted') + $in_default_iterations->sum('renewal_posted'),
                    $country),
                'debt_collector' => Helper::decimalShowing($debt_collector_iterations->sum('origination_posted') + $debt_collector_iterations->sum('interest_posted') + $debt_collector_iterations->sum('renewal_posted'),
                    $country),
                'total'          => Helper::decimalShowing($total_iterations->sum('origination_posted') + $total_iterations->sum('interest_posted') + $total_iterations->sum('renewal_posted'),
                    $country),
            ];
            $employee->fees_collected_format = [
                'current'        => Helper::decimalShowing($current_iterations_collected->sum('origination_posted') + $current_iterations_collected->sum('interest_posted') + $current_iterations_collected->sum('renewal_posted'),
                    $country),
                'in_default'     => Helper::decimalShowing($in_default_iterations_collected->sum('origination_posted') + $in_default_iterations_collected->sum('interest_posted') + $in_default_iterations_collected->sum('renewal_posted'),
                    $country),
                'debt_collector' => Helper::decimalShowing($debt_collector_iterations_collected->sum('origination_posted') + $debt_collector_iterations_collected->sum('interest_posted') + $debt_collector_iterations_collected->sum('renewal_posted'),
                    $country),
                'total'          => Helper::decimalShowing($total_iterations_collected->sum('origination_posted') + $total_iterations_collected->sum('interest_posted') + $total_iterations_collected->sum('renewal_posted'),
                    $country),
            ];

            $employee->debt_format = [
                'current'        => Helper::decimalShowing($current_iterations->sum('debt_posted') + $current_iterations->sum('debt_collection_value_posted'), $country),
                'in_default'     => Helper::decimalShowing($in_default_iterations->sum('debt_posted') + $in_default_iterations->sum('debt_collection_value_posted'), $country),
                'debt_collector' => Helper::decimalShowing($debt_collector_iterations->sum('debt_posted') + $debt_collector_iterations->sum('debt_collection_value_posted'),
                    $country),
                'total'          => Helper::decimalShowing($total_iterations->sum('debt_posted') + $total_iterations->sum('debt_collection_value_posted'), $country),
            ];
            $employee->debt_collected_format = [
                'current'        => Helper::decimalShowing($current_iterations_collected->sum('debt_posted') + $current_iterations_collected->sum('debt_collection_value_posted'),
                    $country),
                'in_default'     => Helper::decimalShowing($in_default_iterations_collected->sum('debt_posted') + $in_default_iterations_collected->sum('debt_collection_value_posted'),
                    $country),
                'debt_collector' => Helper::decimalShowing($debt_collector_iterations_collected->sum('debt_posted') + $debt_collector_iterations_collected->sum('debt_collection_value_posted'),
                    $country),
                'total'          => Helper::decimalShowing($total_iterations_collected->sum('debt_posted') + $total_iterations_collected->sum('debt_collection_value_posted'),
                    $country),
            ];

            $current_score = $default_score = $debt_score = $total_score = 0;
            if (($employee->principal['current'] + $employee->fees['current'] + $employee->debt['current']) > 0) {
                $current_score = ($employee->principal_collected['current'] + $employee->fees_collected['current'] + $employee->debt_collected['current']) /
                    ($employee->principal['current'] + $employee->fees['current'] + $employee->debt['current']) * 100;
            }
            if (($employee->principal['in_default'] + $employee->fees['in_default'] + $employee->debt['in_default']) > 0) {
                $default_score = ($employee->principal_collected['in_default'] + $employee->fees_collected['in_default'] + $employee->debt_collected['in_default']) /
                    ($employee->principal['in_default'] + $employee->fees['in_default'] + $employee->debt['in_default']) * 100;
            }
            if (($employee->principal['debt_collector'] + $employee->fees['debt_collector'] + $employee->debt['debt_collector']) > 0) {
                $debt_score = ($employee->principal_collected['debt_collector'] + $employee->fees_collected['debt_collector'] + $employee->debt_collected['debt_collector']) /
                    ($employee->principal['debt_collector'] + $employee->fees['debt_collector'] + $employee->debt['debt_collector']) * 100;
            }
            if (($employee->principal['total'] + $employee->fees['total'] + $employee->debt['total']) > 0) {
                $total_score = ($employee->principal_collected['total'] + $employee->fees_collected['total'] + $employee->debt_collected['total']) /
                    ($employee->principal['total'] + $employee->fees['total'] + $employee->debt['total']) * 100;
            }

            $employee->score = [
                'current'        => Helper::decimalShowing($current_score, $country) . '%',
                'in_default'     => Helper::decimalShowing($default_score, $country) . '%',
                'debt_collector' => Helper::decimalShowing($debt_score, $country) . '%',
                'total'          => Helper::decimalShowing($total_score, $country) . '%',
            ];
        }

        $data['employees'] = $employees;

        return $data;
    }

    public function cockpit2()
    {
        $data = [];
        $not_in = [3, 4];
        if (auth()->user()->hasAnyRole('admin')) {
            $not_in[] = 1;
        }
        $country = session()->has('country') ? session()->get('country') : '';
        $data['users'] = User::getEmployees($country);
        return view('admin1.pages.users.cockpit', $data);
    }

    public function cockpitData2()
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        $start_date = date('Y-m-d', strtotime('first day of January')) . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 11:59:59';
        $format = config('site.date_format.php');
        if (request('start')) {
            $date = \DateTime::createFromFormat($format, request('start'));
            $start_date = $date->format('Y-m-d') . ' 00:00:00';
        }
        if (request('end')) {
            $date = \DateTime::createFromFormat($format, request('end'));
            $end_date = $date->format('Y-m-d') . ' 23:59:59';
        }

        $start_date = Helper::currentTimezoneToUtcDateTime($start_date);
        $end_date = Helper::currentTimezoneToUtcDateTime($end_date);


        $employees = User::select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'users.id', 'users.role_id', 'roles.name as role')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $employees->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $employees->where('users.country', '=', auth()->user()->country);
            }
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $employees->whereNotIn('users.role_id', [1]);
        }
        $employees = $employees->whereNotIn('users.role_id', [3, 4]);
        if (request('role')) {
            $employees->where('users.role_id', '=', request('role'));
        }
        if (request('user')) {
            $employees->where('users.id', '=', request('user'));
        }

        $employees = $employees->orderBy('name', 'asc')
            ->get();

        $loans = LoanApplication::select('loan_applications.*')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $loans->where('users.country', '=', $country);
        } else if (!auth()->user()->hasRole('super admin')) {
            $loans->where('users.country', '=', auth()->user()->country);
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $loans->whereNotIn('users.role_id', [1]);
        }
        $loans = $loans->whereIn('loan_applications.loan_status', [4, 5, 6])->get();

        $iterations = LoanCalculationHistory::whereIn('loan_id', $loans->pluck('id'))
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->get();

        foreach ($employees as $employee) {
            $loan_id = $loans->where('employee_id', '=', $employee->id)->pluck('id');

            $employee->current = $loans->where('employee_id', '=', $employee->id)->where('loan_status', '=', 4)->count();
            $employee->in_default = $loans->where('employee_id', '=', $employee->id)->where('loan_status', '=', 5)->count();
            $employee->debt_collector = $loans->where('employee_id', '=', $employee->id)->where('loan_status', '=', 6)->count();

            $iteration = $iterations->whereIn('loan_id', $loan_id)->where('payment_amount', '>', 0);

            $employee->principal = $iteration->sum('principal_posted');
            $employee->fees = $iteration->sum('origination_posted') + $iteration->sum('interest_posted') + $iteration->sum('renewal_posted')
                + $iteration->sum('debt_posted') + $iteration->sum('debt_collection_value_posted');

        }

        $unassigned_loan_ids = $loans->where('employee_id', '=', null)->pluck('id');

        $unassigned_iteration = $iterations->whereIn('loan_id', $unassigned_loan_ids)->where('payment_amount', '>', 0);

        $data['unassigned'] = [
            'current'        => $loans->where('employee_id', '=', null)->where('loan_status', '=', 4)->count(),
            'in_default'     => $loans->where('employee_id', '=', null)->where('loan_status', '=', 5)->count(),
            'debt_collector' => $loans->where('employee_id', '=', null)->where('loan_status', '=', 6)->count(),
            'principal'      => $unassigned_iteration->sum('principal_posted'),
            'fees'           => $unassigned_iteration->sum('origination_posted') + $unassigned_iteration->sum('iteration_posted') + $unassigned_iteration->sum('renewal_posted')
                + $unassigned_iteration->sum('debt_posted') + $unassigned_iteration->sum('debt_collection_value_posted'),
        ];

        $iteration = $iterations->whereIn('loan_id', $loans->pluck('id'))->where('payment_amount', '>', 0);

        $data['total'] = [
            'current'        => $loans->where('loan_status', '=', 4)->count(),
            'in_default'     => $loans->where('loan_status', '=', 5)->count(),
            'debt_collector' => $loans->where('loan_status', '=', 6)->count(),
            'principal'      => $iteration->sum('principal_posted'),
            'fees'           => $iteration->sum('origination_posted') + $iteration->sum('iteration_posted') + $iteration->sum('renewal_posted')
                + $iteration->sum('debt_posted') + $iteration->sum('debt_collection_value_posted'),
        ];

        $data['employees'] = $employees;

        return $data;
    }


    public function cockpitExport()
    {

        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : null;
        } else {
            $country = auth()->user()->country;
        }

        $start_date = date('Y-m-d', strtotime('first day of January')) . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 11:59:59';
        $format = config('site.date_format.php');
        if (request('start')) {
            $date = \DateTime::createFromFormat($format, request('start'));
            $start_date = $date->format('Y-m-d') . ' 00:00:00';
        }
        if (request('end')) {
            $date = \DateTime::createFromFormat($format, request('end'));
            $end_date = $date->format('Y-m-d') . ' 23:59:59';
        }

        $start_date = Helper::currentTimezoneToUtcDateTime($start_date);
        $end_date = Helper::currentTimezoneToUtcDateTime($end_date);


        $employees = User::select(DB::raw('concat(users.firstname," ",users.lastname) as name'), 'users.id', 'users.role_id', 'roles.name as role')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $employees->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $employees->where('users.country', '=', auth()->user()->country);
            }
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $employees->whereNotIn('users.role_id', [1]);
        }
        $employees = $employees->whereNotIn('users.role_id', [3, 4]);

        $employees = $employees->orderBy('name', 'asc')
            ->get();

        $loans = LoanApplication::select('loan_applications.*')
            ->leftJoin('users', 'users.id', '=', 'loan_applications.client_id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $loans->where('users.country', '=', $country);
        } else if (!auth()->user()->hasRole('super admin')) {
            $loans->where('users.country', '=', auth()->user()->country);
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $loans->whereNotIn('users.role_id', [1]);
        }
        $loans = $loans->whereIn('loan_applications.loan_status', [4, 5, 6])->get();

        $iterations = LoanCalculationHistory::whereIn('loan_id', $loans->pluck('id'))
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->get();

        $data = [];

        foreach ($employees as $employee) {
            $loan_id = $loans->where('employee_id', '=', $employee->id)->pluck('id');

            $employee->current = $loans->where('employee_id', '=', $employee->id)->where('loan_status', '=', 4)->count();
            $employee->in_default = $loans->where('employee_id', '=', $employee->id)->where('loan_status', '=', 5)->count();
            $employee->debt_collector = $loans->where('employee_id', '=', $employee->id)->where('loan_status', '=', 6)->count();

            $iteration = $iterations->whereIn('loan_id', $loan_id)->where('payment_amount', '>', 0);

            $employee->principal = $iteration->sum('principal_posted');
            $employee->fees = $iteration->sum('origination_posted') + $iteration->sum('interest_posted') + $iteration->sum('renewal_posted')
                + $iteration->sum('debt_posted') + $iteration->sum('debt_collection_value_posted');

            $data[] = [
                'Name'                => $employee->name,
                'Role'                => $employee->role,
                'Current'             => $employee->current,
                'In Default'          => $employee->in_default,
                'Debt Collector'      => $employee->debt_collector,
                'Principal Collected' => $employee->principal,
                'Fees Collected'      => $employee->fees,
            ];

        }

        $unassigned_loan_ids = $loans->where('employee_id', '=', null)->pluck('id');

        $unassigned_iteration = $iterations->whereIn('loan_id', $unassigned_loan_ids)->where('payment_amount', '>', 0);

        $data[] = [
            'Name'                => 'Unassigned',
            'Role'                => '',
            'Current'             => $loans->where('employee_id', '=', null)->where('loan_status', '=', 4)->count(),
            'In Default'          => $loans->where('employee_id', '=', null)->where('loan_status', '=', 5)->count(),
            'Debt Collector'      => $loans->where('employee_id', '=', null)->where('loan_status', '=', 6)->count(),
            'Principal Collected' => $unassigned_iteration->sum('principal_posted'),
            'Fees Collected'      => $unassigned_iteration->sum('origination_posted') + $unassigned_iteration->sum('iteration_posted') + $unassigned_iteration->sum('renewal_posted')
                + $unassigned_iteration->sum('debt_posted') + $unassigned_iteration->sum('debt_collection_value_posted'),
        ];

        $iteration = $iterations->whereIn('loan_id', $loans->pluck('id'))->where('payment_amount', '>', 0);

        $data[] = [
            'Name'                => 'Total',
            'Role'                => '',
            'Current'             => $loans->where('loan_status', '=', 4)->count(),
            'In Default'          => $loans->where('loan_status', '=', 5)->count(),
            'Debt Collector'      => $loans->where('loan_status', '=', 6)->count(),
            'Principal Collected' => $iteration->sum('principal_posted'),
            'Fees Collected'      => $iteration->sum('origination_posted') + $iteration->sum('iteration_posted') + $iteration->sum('renewal_posted')
                + $iteration->sum('debt_posted') + $iteration->sum('debt_collection_value_posted'),
        ];

        $filename = 'cockpit' . '-' . date('Ymd') . '-' . time();
        Excel::create($filename, function ($excel) use ($data) {
            $excel->setTitle('Report OF ' . date('d-m-Y H:i:s'));
            //Chain the setters
            $excel->sheet('Report', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx', public_path() . '/uploads/excel');

        $data = [];
        $data['url'] = URL::to('/') . '/uploads/excel/' . $filename . '.xlsx';

        return $data;
    }

}
