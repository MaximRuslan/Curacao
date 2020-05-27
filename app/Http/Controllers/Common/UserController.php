<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Library\EmailHelper;
use App\Library\Helper;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Documents;
use App\Models\Relationship;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBank;
use App\Models\UserDepartment;
use App\Models\UserInfo;
use App\Models\UserReference;
use App\Models\UserStatus;
use App\Models\UserTerritory;
use App\Models\UserWork;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:super admin|admin|processor|auditor|debt collector|loan approval|credit and processing')
            ->only([
                'index',
                'getList',
                'infoShow',
                'ajaxWorksGet',
                'ajaxWorksEdit',
                'ajaxBanksGet',
                'ajaxReferencesGet',
            ]);
        $this->middleware('role:super admin|admin|processor')->only([
            'ajaxUpdate',
            'edit',
            'ajaxWorksStore',
            'ajaxWorksdelete',
            'workingType',
            'create',
            'ajaxStore',
            'destroy',
            'ajaxBanksUpdate',
            'ajaxUserTerritoryBanks',
            'ajaxReferencesUpdate',
            'show',
            'ajaxWalletAdd',
            'walletDatatable',
        ]);
    }

    /**
     * @desc users crud
     * @date 18 Jun 2018 14:40
     */
    public function index()
    {
        $data = [];
        $data['cash_back_payment_types'] = config('site.cash_back_payment_types');
        return view('common.users.index', $data);
    }

    public function getList()
    {
        $filterUsers = [auth()->user()->id, '1'];

        $user = User::with('role')->whereNotIn('users.id', $filterUsers);

        if (auth()->user()->hasRole('super admin')) {
            $country = session()->has('country') ? session()->get('country') : '';
            if ($country != '') {
                $user->where(['users.country' => $country]);
            }
        } else if (auth()->user()->hasRole('admin')) {
            $user->whereNotIn('users.role_id', ['1', '5'])
                ->where(['users.country' => auth()->user()->country]);
        } else {
            $user->where('users.role_id', '=', '3')
                ->where(['users.country' => auth()->user()->country]);
        }

        if (request('user_id')) {
            $user->where('users.id', '=', request('user_id'));
        }

        $user->select('users.*', 'countries.name as country_name', 'user_status.title as status_name',
            DB::raw('(select sum(amount) from wallets where wallets.user_id=users.id and wallets.deleted_at is null) as wallet'))
            ->where('users.role_id', '!=', '4')
            ->leftJoin('countries', 'countries.id', '=', 'users.country')
            ->leftJoin('user_status', 'user_status.id', '=', 'users.status')
            ->groupBy('users.id');

        return DataTables::of($user)
            ->addColumn('username', function ($row) {
                return ucwords(strtolower($row->lastname . " " . $row->firstname));
            })
            ->addColumn('wallet', function ($data) {
                if ($data->wallet == null) {
                    return "0.00";
                }
                return number_format($data->wallet, 2);
            })
            ->addColumn('is_verified', function ($data) {
                return $data->is_verified == 1 ? 'Yes' : 'No';
            })
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';

                if (auth()->user()->hasRole('super admin') || (auth()->user()->hasRole('admin') && $data->role_id == 3)) {
                    $html .= "<a href='" . url()->route('users.edit', $data->id) . "' class='$iconClass'
                                data-toggle='tooltip' title='edit'>
                            <i class='fa fa-pencil'></i>
                        </a>";
                }

                if (auth()->user()->hasRole('super admin|admin') || (!auth()->user()->hasRole('super admin|admin') && $data->role_id == 3)) {
                    $html .= "<a href='" . url()->route('users.info', $data->id) . "' class='$iconClass ViewInfo'
                                data-toggle='tooltip' title='View Info'>
                            <i class='fa fa-eye'></i>
                        </a>";
                }

                //$html .= "<a href='javascript:;' data-modal-id='deleteUser' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";

                if ($data->role_id == 3 && auth()->user()->hasRole('super admin|admin|processor')) {
                    $html .= "<a href='javascript:;' data-id='$data->id' class='$iconClass AddAmount' 
                                    data-name='" . ucwords(strtolower($data->lastname . ' ' . $data->firstname)) . "' 
                                    data-client_id='" . $data->id_number . "'
                                    data-balance='" . $data->wallet . "' data-toggle='tooltip' title='Wallet'>
                                <i class='fa fa-google-wallet'></i>
                            </a>";
                }

                if ($data->signature != null && $data->role_id == 3) {
                    $html .= '<a href="' . asset('uploads/' . $data->signature) . '" target="_blank" download="" 
                                    class="btn btn-sm waves-effect btn-info" data-toggle="tooltip" title="Signature">
                                <i class="fa fa-paperclip"></i>
                              </a>';
                }


                $html .= "</div>";

                return $html;
            })
            ->make();
    }

    public function create()
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';

        $data['roles'] = Role::select('*');
        if (auth()->user()->hasRole('admin')) {
            $data['roles']->whereNotIn('name', ['super admin', 'admin']);
        } elseif (auth()->user()->hasRole('processor')) {
            $data['roles']->where('name', '=', 'client');
        }
        $data['lang'] = config('site.language');
        $data['roles'] = $data['roles']
            ->orderBy('name', 'asc')
            ->get();
        $data['roles'] = $data['roles']->map(function ($item, $key) {
            $item->name = ucwords(strtolower($item->name));
            return $item;
        });
        /* $data['departments'] = UserDepartment::select('id', 'title')
             ->orderBy('title', 'asc')
             ->get();
         $data['departments'] = $data['departments']->map(function ($item, $key) {
             $item->title = ucwords(strtolower($item->title));
             return $item;
         });*/
        if (!auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', auth()->user()->country)
                ->pluck('name', 'id');
            $data['countries'] = $data['countries']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
            $data['territories'] = UserTerritory::where('id', '=', auth()->user()->territory)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('id', '=', auth()->user()->branch)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['territories'] = $data['territories']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
            $data['branches'] = $data['branches']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        } else if (auth()->user()->hasRole('super admin') && $country != '') {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', $country)
                ->pluck('name', 'id');
            $data['countries'] = $data['countries']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
            $data['territories'] = UserTerritory::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['territories'] = $data['territories']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
            $data['branches'] = $data['branches']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        } else {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->pluck('name', 'id');
            $data['countries'] = $data['countries']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        }
        $data['status'] = UserStatus::select('id', 'title', 'role')
            ->orderBy('title', 'asc')
            ->get();
        $data['status'] = $data['status']->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        });
        $data['relationships'] = Relationship::pluck('title', 'id');
        return view('common.users.create', $data);
    }

    public function ajaxStore()
    {
        $inputs = request()->all();
        $format = config('site.date_format.php');

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
        $user = DB::table('users')->where('id_number', '=', request('id_number'))
            ->first();
        if ($user != null) {
            $data = [
                'status'  => false,
                'type'    => 'id_number',
                'message' => 'ID number is already taken'
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
        if ((!request('telephone') && !request('cellphone')) || ((request('telephone') && count(request('telephone')) == 0) && (request('cellphone') && count(request('cellphone')) == 0))) {
            $data = [];
            $data['status'] = false;
            $data['type'] = 'phone';
            $data['message'] = 'Minimum one telephone or cellphone required.';
            return $data;
        }
        if (request()->hasFile('profile_pic')) {
            $profile = time() . '_' . request()->file('profile_pic')->getClientOriginalName();
            $path = request()->profile_pic->move(public_path('uploads'), $profile);
            $inputs['profile_pic'] = $profile;
        }

        if (request()->hasFile('scan_id')) {
            $profile = time() . '_' . request()->file('scan_id')->getClientOriginalName();
            $path = request()->scan_id->move(public_path('uploads'), $profile);
            $inputs['scan_id'] = $profile;
        }

        /* if (request()->hasFile('other_document')) {
             $doc = time() . '_' . request()->file('other_document')->getClientOriginalName();
             $path = request()->other_document->move(public_path('uploads'), $doc);
             $inputs['other_document'] = $doc;
         }*/

        if (request()->hasFile('address_proof')) {
            $proof = time() . '_' . request()->file('address_proof')->getClientOriginalName();
            $path = request()->address_proof->move(public_path('uploads'), $proof);
            $inputs['address_proof'] = $proof;
        }

        if (request()->hasFile('payslip1')) {
            $payslip1 = time() . '_' . request()->file('payslip1')->getClientOriginalName();
            $path = request()->payslip1->move(public_path('uploads'), $payslip1);
            $inputs['payslip1'] = $payslip1;
        }

        if (request()->hasFile('payslip2')) {
            $payslip2 = time() . '_' . request()->file('payslip2')->getClientOriginalName();
            $path = request()->payslip2->move(public_path('uploads'), $payslip2);
            $inputs['payslip2'] = $payslip2;
        }

        $password = str_random(6);
        $inputs['password'] = bcrypt($password);
        $inputs['is_verified'] = 0;
        if (request('dob')) {
            $date = \DateTime::createFromFormat($format, request('dob'));
            $inputs['dob'] = $date->format('Y-m-d');
        } else {
            $inputs['dob'] = NULL;
        }
        if (request('exp_date')) {
            $date = \DateTime::createFromFormat($format, request('exp_date'));
            $inputs['exp_date'] = $date->format('Y-m-d');
        } else {
            $inputs['exp_date'] = NULL;
        }

        if (request('pp_exp_date')) {
            $date = \DateTime::createFromFormat($format, request('pp_exp_date'));
            $inputs['pp_exp_date'] = $date->format('Y-m-d');
        } else {
            $inputs['pp_exp_date'] = NULL;
        }


        $user = User::create($inputs);

        if (request('branch')) {
            $user->userBranches()->sync(request('branch'));
        }

        if (request('other_document')) {
            foreach (request('other_document') as $key => $value) {
                $doc = time() . '_' . $value->getClientOriginalName();
                $path = $value->move(public_path('uploads'), $doc);
                Documents::create([
                    'main_id'  => $user->id,
                    'type'     => '1',
                    'name'     => request('other_document_name')[$key],
                    'document' => $doc
                ]);
            }
        }

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

        $role = Role::find($user->role_id);
        $user->assignRole([$role->name]);

        $data = [];
        $data['status'] = true;
        $data['user_id'] = $user->id;
        $data['role_id'] = $user->role_id;
        if ($user->sent_email == null || $user->sent_email == 0) {
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
            Log::info('verification mail sent to ' . $user->email . '.');
            $user->update([
                'sent_email' => '1'
            ]);
        }
        return $data;
    }

    public function ajaxUpdate(User $user)
    {
        $inputs = request()->all();
        $format = config('site.date_format.php');
        $custom_user = DB::table('users')
            ->where('email', '=', request('email'))
            ->where('id', '!=', $user->id)
            ->first();

        if ($custom_user != null) {
            $data = [
                'status'  => false,
                'message' => 'Email is already taken'
            ];
            return $data;
        }
        $custom_user = DB::table('users')
            ->where('id_number', '=', request('id_number'))
            ->where('id', '!=', $user->id)
            ->first();
        if ($custom_user != null) {
            $data = [
                'status'  => false,
                'type'    => 'id_number',
                'message' => 'ID number is already taken'
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

        $this->validate(request(), User::validationRules($inputs, $length, $user->id));
        if ((!request('telephone') && !request('cellphone')) || ((request('telephone') && count(request('telephone')) == 0) && (request('cellphone') && count(request('cellphone')) == 0))) {
            $data = [];
            $data['status'] = false;
            $data['type'] = 'phone';
            $data['message'] = 'Minimum one telephone or cellphone required.';
            return $data;
        }
        if (request()->hasFile('profile_pic')) {
            if ($user->profile_pic != '') {
                Storage::delete(public_path('uploads/' . $user->profile_pic));
            }
            $profile = time() . '_' . request()->file('profile_pic')->getClientOriginalName();
            request()->file('profile_pic')->move(public_path('uploads'), $profile);
            $inputs['profile_pic'] = $profile;
        } else {
            if (request()->removeImage != 'true') {
                $inputs['profile_pic'] = $user->profile_pic;
            } else {
                if ($user->profile_pic != '') {
                    Storage::delete(public_path('uploads/' . $user->profile_pic));
                    $inputs['profile_pic'] = '';
                }
            }
        }
        if (request()->hasFile('address_proof')) {
            if ($user->address_proof != '') {
                Storage::delete(public_path('uploads/' . $user->address_proof));
            }
            $proof = time() . '_' . request()->file('address_proof')->getClientOriginalName();
            request()->file('address_proof')->move(public_path('uploads'), $proof);
            $inputs['address_proof'] = $proof;
        } else {
            if (request()->removeAddressProof != 'true') {
                $inputs['address_proof'] = $user->address_proof;
            } else {
                if ($user->address_proof != '') {
                    Storage::delete(public_path('uploads/' . $user->address_proof));
                    $inputs['address_proof'] = '';
                }
            }
        }
        \Log::info(request()->all());
        if (request()->hasFile('payslip1')) {
            if ($user->payslip1 != '') {
                Storage::delete(public_path('uploads/' . $user->payslip1));
            }
            $payslip1 = time() . '_' . request()->file('payslip1')->getClientOriginalName();
            request()->file('payslip1')->move(public_path('uploads'), $payslip1);
            $inputs['payslip1'] = $payslip1;
        } else {
            if (request()->removePayslip1 != 'true') {
                $inputs['payslip1'] = $user->payslip1;
            } else {
                if ($user->payslip1 != '') {
                    Storage::delete(public_path('uploads/' . $user->payslip1));
                    $inputs['payslip1'] = '';
                }
            }
        }
        if (request()->hasFile('payslip2')) {
            if ($user->payslip2 != '') {
                Storage::delete(public_path('uploads/' . $user->payslip2));
            }
            $payslip2 = time() . '_' . request()->file('payslip2')->getClientOriginalName();
            request()->file('payslip2')->move(public_path('uploads'), $payslip2);
            $inputs['payslip2'] = $payslip2;
        } else {
            if (request()->removePayslip2 != 'true') {
                $inputs['payslip2'] = $user->payslip2;
            } else {
                if ($user->payslip2 != '') {
                    Storage::delete(public_path('uploads/' . $user->payslip2));
                    $inputs['payslip2'] = '';
                }
            }
        }
        /*if (request()->hasFile('other_document')) {
            if ($user->other_document != '') {
                Storage::delete(public_path('uploads/' . $user->other_document));
            }
            $doc = time() . '_' . request()->file('other_document')->getClientOriginalName();
            request()->file('other_document')->move(public_path('uploads'), $doc);
            $inputs['other_document'] = $doc;
        } else {
            if (request()->removeOtherDocument != 'true') {
                $inputs['other_document'] = $user->other_document;
            } else {
                if ($user->other_document != '') {
                    Storage::delete(public_path('uploads/' . $user->other_document));
                    $inputs['other_document'] = '';
                }
            }
        }*/

        if (request()->hasFile('scan_id')) {
            if ($user->scan_id != '') {
                Storage::delete(public_path('uploads/' . $user->scan_id));
            }
            $profile = time() . '_' . request()->file('scan_id')->getClientOriginalName();
            $path = request()->scan_id->move(public_path('uploads'), $profile);
            $inputs['scan_id'] = $profile;
        } else {
            if (request()->removeScanId != 'true') {
                $inputs['scan_id'] = $user->scan_id;
            } else {
                if ($user->scan_id != '') {
                    Storage::delete(public_path('uploads/' . $user->scan_id));
                    $inputs['profile_pic'] = '';
                }
            }
        }
        if (request('dob')) {
            $date = \DateTime::createFromFormat($format, request('dob'));
            $inputs['dob'] = $date->format('Y-m-d');
        } else {
            $inputs['dob'] = NULL;
        }
        if (request('exp_date')) {
            $date = \DateTime::createFromFormat($format, request('exp_date'));
            $inputs['exp_date'] = $date->format('Y-m-d');
        } else {
            $inputs['exp_date'] = NULL;
        }

        if (request('pp_exp_date')) {
            $date = \DateTime::createFromFormat($format, request('pp_exp_date'));
            $inputs['pp_exp_date'] = $date->format('Y-m-d');
        } else {
            $inputs['pp_exp_date'] = NULL;
        }


        if ($user->email != $inputs['email']) {
            $password = str_random(6);
            $inputs['password'] = bcrypt($password);
            $inputs['is_verified'] = 0;
            $user->update($inputs);
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
        } else {
            $user->update($inputs);
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
                    'document' => $doc
                ]);
            }
        }

        if (request('branch')) {
            $user->userBranches()->sync(request('branch'));
        }

        UserInfo::where('user_id', '=', $user->id)->delete();
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

        $role = Role::find($user->role_id);
        $user->syncRoles([$role->name]);

        $data = [];
        $data['user_id'] = $user->id;
        $data['role_id'] = $user->role_id;
        $data['status'] = true;
        return $data;
    }

    public function edit(User $user)
    {
        $data = [];
        $country = session()->has('country') ? session()->get('country') : '';
        $data['roles'] = Role::select('*');
        if (auth()->user()->hasRole('admin')) {
            $data['roles']->whereNotIn('name', ['super admin', 'admin']);
        } elseif (auth()->user()->hasRole('processor')) {
            $data['roles']->where('name', '=', 'client');
        }
        $data['roles'] = $data['roles']
            ->orderBy('name', 'asc')
            ->get();
        $data['lang'] = config('site.language');
        $data['departments'] = UserDepartment::select(['id', 'title'])->get();
        if (!auth()->user()->hasRole('super admin')) {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', auth()->user()->country)
                ->pluck('name', 'id');
            $data['territories'] = UserTerritory::where('id', '=', auth()->user()->territory)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('id', '=', auth()->user()->branch)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
        } else if (auth()->user()->hasRole('super admin') && $country != '') {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->where('id', '=', $country)
                ->pluck('name', 'id');
            $data['countries'] = $data['countries']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
            $data['territories'] = UserTerritory::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['branches'] = Branch::where('country_id', '=', $country)
                ->orderBy('title', 'asc')
                ->pluck('title', 'id');
            $data['territories'] = $data['territories']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
            $data['branches'] = $data['branches']->map(function ($item, $key) {
                return ucwords(strtolower($item));
            });
        } else {
            $data['countries'] = Country::orderBy('name', 'asc')
                ->pluck('name', 'id');
        }
        $data['status'] = UserStatus::select('id', 'title', 'role')
            ->orderBy('title', 'asc')
            ->get();
        $data['status'] = $data['status']->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        });
        $data['edit_user'] = $user;
        $data['relationships'] = Relationship::pluck('title', 'id');
        return view('common.users.create', $data);
    }

    public function show($id)
    {
        $user = User::find($id);
        $format = config('site.date_format.php');
        if ($user->profile_pic) {
            $user->profile_pic = asset('uploads/' . $user->profile_pic);
        } else {
            $user->profile_pic = '';
        }
        if ($user->address_proof) {
            $user->address_proof = asset('uploads/' . $user->address_proof);
        } else {
            $user->address_proof = '';
        }
        if ($user->payslip1) {
            $user->payslip1 = asset('uploads/' . $user->payslip1);
        } else {
            $user->payslip1 = '';
        }
        $documents = Documents::select('name', 'id', 'document')
            ->where('main_id', '=', $user->id)
            ->where('type', '=', '1')
            ->get();
        foreach ($documents as $key => $value) {
            $value->document = asset('uploads/' . $value->document);
        }
//        $user->other_document = asset('uploads/' . $user->other_document);
        if ($user->payslip2) {
            $user->payslip2 = asset('uploads/' . $user->payslip2);
        } else {
            $user->payslip2 = '';
        }
        if ($user->scan_id) {
            $user->scan_id = asset('uploads/' . $user->scan_id);
        } else {
            $user->scan_id = '';
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
            'id'             => ["type" => "hidden", 'value' => $user->id],
            'firstname'      => ["type" => "text", 'value' => $user->firstname],
            'lastname'       => ["type" => "text", 'value' => $user->lastname],
            'email'          => ["type" => "text", 'value' => $user->email],
            'mobile_no'      => ["type" => "text", 'value' => $user->mobile_no],
            'address'        => ["type" => "textarea", 'value' => $user->address],
            'lang'           => ["type" => "select", 'value' => $user->lang],
            'role_id'        => ["type" => "select", 'value' => $user->role_id],
            'department'     => ["type" => "select", 'value' => $user->department],
            'territory'      => ["type" => "select", 'value' => $user->territory],
            'branch'         => ["type" => "select", 'value' => $user->branch],
            'status'         => ["type" => "select", 'value' => $user->status],
            'sex'            => ["type" => "radio", 'checkedValue' => $user->sex],
            'profile_pic'    => ["type" => "image", 'value' => $user->profile_pic],
            'id_number'      => ["type" => 'text', 'value' => $user->id_number],
            'dob'            => ["type" => 'text', 'value' => $user->dob],
            'place_of_birth' => ["type" => 'text', 'value' => $user->place_of_birth],
            'country'        => [
                "type"      => 'select-territory',
                'value'     => $user->country,
                'territory' => $user->territory,
                'branch'    => $user->userBranches->pluck('id')
            ],

            'country_code'      => [
                'type'  => 'text',
                'value' => $country_code
            ],
            'phone_length'      => [
                'type'  => 'text',
                'value' => $phone_length
            ],
            'civil_status'      => ["type" => 'select', 'value' => $user->civil_status],
            'spouse_first_name' => ["type" => 'text', 'value' => $user->spouse_first_name],
            'spouse_last_name'  => ["type" => 'text', 'value' => $user->spouse_last_name],
            'exp_date'          => ["type" => 'text', 'value' => $user->exp_date],
            'pp_number'         => ["type" => 'text', 'value' => $user->pp_number],
            'pp_exp_date'       => ["type" => 'text', 'value' => $user->pp_exp_date],
            'scan_id'           => ["type" => 'image', 'value' => $user->scan_id],
            'address_proof'     => ["type" => 'image', 'value' => $user->address_proof],
            'payslip1'          => ["type" => 'image', 'value' => $user->payslip1],
            'payslip2'          => ["type" => 'image', 'value' => $user->payslip2],
            'other_document'    => ["type" => 'image', 'value' => $user->other_document],
        ];
        $telephones = UserInfo::where('user_id', '=', $id)->where('type', '=', 1)->pluck('value');
        $cellphones = UserInfo::where('user_id', '=', $id)->where('type', '=', 2)->pluck('value');
        $emails = UserInfo::where('user_id', '=', $id)->where('type', '=', 3)->pluck('value');
        return response()->json([
            "status"          => "success",
            "inputs"          => $filteredArr,
            'telephones'      => $telephones,
            'cellphones'      => $cellphones,
            'other_documents' => $documents,
            'emails'          => $emails
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    public function infoShow($user)
    {
        $data = [];
        $user = User::select('users.*', 'user_status.title as status', 'countries.name as country',
            'user_territories.title as territory', 'countries.country_code as country_code',
            DB::raw('group_concat(branches.title) as branch'))
            ->leftJoin('user_status', 'user_status.id', '=', 'users.status')
            ->leftJoin('countries', 'countries.id', '=', 'users.country')
            ->leftJoin('user_territories', 'user_territories.id', '=', 'users.territory')
            ->leftJoin('user_branches', 'user_branches.user_id', '=', 'users.id')
            ->leftJoin('branches', 'branches.id', '=', 'user_branches.branch_id')
            ->groupBy('users.id')
            ->where('users.id', '=', $user)
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
            $user['extra_emails'] = $user_infos['3'];
        }

        $data['user'] = $user;
        $data['relationships'] = Relationship::pluck('title', 'id');
        if (request('json') == 'data') {
            return $data;
        }
        return view('common.users.view', $data);
    }

    public function ajaxDeleteDocument(Documents $document)
    {
        $data = [];
        Storage::delete(public_path('uploads/' . $document->document));
        $data['status'] = $document->delete();
        return $data;
    }

    /**
     * @desc works crud in users
     * @date 18 Jun 2018 14:40
     */
    public function ajaxWorksGet(User $user)
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

    public function ajaxWorksStore(User $user)
    {
        $data = [];
        $format = config('site.date_format.php');
        $country = Country::find($user->country);
        $length = 10;
        if ($country != null) {
            if ($country->phone_length != null) {
                $length = $country->phone_length;
            }
        }
        $validator = Validator::make(request()->all(), UserWork::validationRules($length));
        if ($validator->fails()) {
            $data['status'] = false;
            $data['errors'] = $validator->errors();
        } else {
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
                if (request('contract_expires') && request('contract_expires')) {
                    $date = \DateTime::createFromFormat($format, request('contract_expires'));
                    $work->contract_expires = $date->format('Y-m-d');
                }
                if (!$work->save()) {
                    $data['status'] = false;
                } else {
                    $data['status'] = true;
                }
            } else {
                $data['status'] = false;
            }
        }
        return $data;
    }

    public function ajaxWorksEdit(User $user, UserWork $work)
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
            $country = Country::find($user->country);
            $country_code = 0;
            $phone_length = 10;
            if ($country != null) {
                $country_code = $country->country_code;
                if ($country->phone_length != null) {
                    $phone_length = $country->phone_length;
                }
            }
            $data['work'] = [
                'id'                        => ["type" => "hidden", 'value' => $work->id],
                'employer'                  => ["type" => "text", 'value' => $work->employer],
                'address'                   => ["type" => "textarea", 'value' => $work->address],
                'telephone'                 => ["type" => "text", 'value' => $work->telephone],
                'cellphone'                 => ["type" => "text", 'value' => $work->cellphone],
                'position'                  => ["type" => "text", 'value' => $work->position],
                'employed_since'            => ["type" => "text", 'value' => $work->employed_since],
                'employment_type'           => ["type" => "select", 'value' => $work->employment_type],
                'contract_expires'          => ["type" => "text", 'value' => $work->contract_expires],
                'department'                => ["type" => "text", 'value' => $work->department],
                'supervisor_name'           => ["type" => "text", 'value' => $work->supervisor_name],
                'supervisor_telephone'      => ["type" => "text", 'value' => $work->supervisor_telephone],
                'salary'                    => ["type" => "number", 'value' => $work->salary],
                'payment_frequency'         => ["type" => "select", 'value' => $work->payment_frequency],
                'supervisor_telephone_code' => ["type" => "text", 'value' => $work->supervisor_telephone_code],
                'telephone_code'            => ["type" => "text", 'value' => $work->telephone_code],
                'extension'                 => ["type" => "text", 'value' => $work->extension],
                'cellphone_code'            => ["type" => "text", 'value' => $work->cellphone_code],
                'country_code'              => ['type' => 'text', 'value' => $country_code],
                'phone_length'              => ['type' => 'text', 'value' => $phone_length]
            ];
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        return $data;
    }

    public function ajaxWorksDelete(User $user, UserWork $work)
    {
        $data = [];
        if ($work->user_id == $user->id) {
            $data['status'] = $work->delete();
        } else {
            $data['status'] = false;
        }
        return $data;
    }

    public function workingType(User $user)
    {
        $data = [];
        $user->update([
            'working_type' => request('type')
        ]);
        return $data;
    }

    /**
     * @desc banks users
     * @date 18 Jun 2018 14:40
     */
    public function ajaxBanksGet(User $user)
    {
        $data = [];
        $data['banks_data'] = Bank::select('name', 'id')
            ->where('country_id', '=', $user->country)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');

        $data['banks'] = UserBank::where('user_id', '=', $user->id)
            ->get();
        return $data;
    }

    public function ajaxBanksUpdate(user $user)
    {
        $data = [];
        $validator = Validator::make(request()->all(), UserBank::validationRules(), UserBank::validationMessage());
        if ($validator->fails()) {
            $data['status'] = false;
            $data['inputs'] = request()->all();
            $data['banks'] = Bank::where('country_id', '=', $user->country)
                ->orderBy('name', 'asc')
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

    public function ajaxUserTerritoryBanks()
    {
        $data = [];
        $user = User::find(request('user_id'));
        $data['banks'] = Bank::select('banks.name', 'id')
            ->where('country_id', '=', $user->country)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');
        return $data;
    }

    /**
     * @desc user references
     * @date 18 Jun 2018 14:40
     */
    public function ajaxReferencesGet(User $user)
    {
        $data = [];
        $country_code = 0;
        $country = Country::find($user->country);
        $phone_length = 10;
        if ($country != null) {
            $country_code = $country->country_code;
            if ($country->phone_length != null) {
                $phone_length = $country->phone_length;
            }
        }
        $data['country_code'] = $country_code;
        $data['phone_length'] = $phone_length;
        $data['references'] = UserReference::where('user_id', '=', $user->id)
            ->get();
        return $data;
    }

    public function ajaxReferencesUpdate(user $user)
    {
        $data = [];
        $country = Country::find($user->country);
        $length = 10;
        if ($country != null) {
            if ($country->phone_length != null) {
                $length = $country->phone_length;
            }
        }
        $validator = Validator::make(request()->all(), UserReference::validationRules($length), UserReference::validationMessage());
        if ($validator->fails()) {
            $data['status'] = false;
            $data['inputs'] = request()->all();
            $data['errors'] = $validator->errors();
            $phone_length = 10;
            if ($country != null) {
                $country_code = $country->country_code;
                if ($country->phone_length != null) {
                    $phone_length = $country->phone_length;
                }
            }
            $data['phone_length'] = $phone_length;
            $country_code = 0;
            $country = Country::find($user->country);
            if ($country != null) {
                $country_code = $country->country_code;
            }
            $data['country_code'] = $country_code;
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
//            if ($user->sent_email == null || $user->sent_email == 0) {
//                Mail::send('emails.confirm-email', ['user' => $user], function ($message) use ($user) {
//                    $message->from(config('mail.from.address'), config('mail.from.name'));
//                    $message->to($user->email);
//$message->bcc(config('site.bcc_users'));
//                    $message->subject(config('mail.from.name') .': Verify your online account.');
//                });
//                \Log::info('verification mail sent to ' . $user->email . '.');
//                $user->update([
//                    'sent_email' => '1'
//                ]);
//            }
            $data['status'] = true;
        }
        return $data;
    }


    /**
     * @desc user wallets
     * @date 18 Jun 2018 14:40
     */
    public function ajaxWalletAdd(User $user)
    {
        $this->validate(request(), Wallet::validationRules());
        $data = [];
        foreach (request('amount') as $key => $value) {
            if ($value != '') {
                $inputs['amount'] = $value;
                $inputs['type'] = $key;
                $inputs['user_id'] = $user->id;
                $inputs['notes'] = request('notes');
                $inputs['transaction_payment_date'] = date('Y-m-d', strtotime(request('transaction_payment_date')));
                Wallet::create($inputs);
            }
        }
        foreach (request('cashback_amount') as $key => $value) {
            if ($value != '') {
                $inputs['amount'] = '-' . $value;
                $inputs['type'] = $key;
                $inputs['user_id'] = $user->id;
                $inputs['notes'] = request('notes');
                $inputs['transaction_payment_date'] = date('Y-m-d', strtotime(request('transaction_payment_date')));
                Wallet::create($inputs);
            }
        }
        return $data;
    }

    public function walletDatatable()
    {
        $data = Wallet::where('user_id', '=', request('user_id'));

        return DataTables::of($data)
            ->editColumn('type', function ($data) {
                return config('site.payment_types.' . $data->type) ? config('site.payment_types.' . $data->type) : ($data->type == 0 ? "Loan" : ($data->type == 7 ? "Credit Deduct" : ""));
            })
            ->addColumn('transaction_payment_date', function ($row) {
                return Helper::date_time_to_current_timezone($row->transaction_payment_date);
            })
            ->addColumn('created_at', function ($row) {
                return Helper::date_time_to_current_timezone($row->created_at);
            })
            ->make(true);
    }


    public function ajaxUserBanks(User $user)
    {
        $data = [];

        $data['banks'] = Bank::select('banks.name', 'banks.id', 'user_banks.account_number', 'banks.transaction_fee_type', 'banks.tax_transaction', 'banks.transaction_fee')
            ->leftJoin('user_banks', function ($join) {
                $join->on('user_banks.bank_id', '=', 'banks.id')
                    ->whereNull('user_banks.deleted_at');
            })
            ->where('user_banks.user_id', '=', $user->id)
            ->orderBy('banks.name', 'asc')
            ->get();

        $data['branches'] = Branch::where('country_id', '=', $user->country)
            ->pluck('title', 'id');

        return $data;
    }

    public function changeEmail()
    {
        $this->validate(request(), ['email' => 'required|unique:users,email,' . auth()->user()->id]);
        $user = auth()->user();
        $user->update([
            'email'       => request('email'),
            'is_verified' => 0
        ]);
        try {
            EmailHelper::emailConfigChanges('user');
            Mail::send('emails.confirm-email', ['user' => $user], function ($message) use ($user) {
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
}
