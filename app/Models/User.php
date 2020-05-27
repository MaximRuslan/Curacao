<?php

namespace App\Models;

use App\Mail\DeleteUserMail;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'new_email',
        'lang',
        'password',
        'role_id',
        'mobile_no',
        'sex',
        'department',
        'territory',
        'id_number',
        'status',
        'is_verified',
        'profile_pic',
        'dob',
        'place_of_birth',
        'address',
        'country',
        'civil_status',
        'spouse_first_name',
        'spouse_last_name',
        'exp_date',
        'pp_number',
        'pp_exp_date',
        'scan_id',
        'contact_person',
        'transaction_type',
        'transaction_fee',
        'commission_type',
        'commission_fee',
        'commission',
        'working_type',
        'payslip2',
        'payslip1',
        'address_proof',
        'sent_email',
        'last_activity',
        'terms_accepted',
        'signature',
        'how_much_loan',
        'repay_loan_2_weeks',
        'have_bank_loan',
        'have_bank_account',
        'web_registered',
        'referral_code',
        'referred_by',
        'referral_status',
        'complete_profile',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function applications()
    {
        return $this->hasMany(LoanApplication::class, 'id', 'user_id');
    }

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token));
    }

    public function userBanks()
    {
        return $this->hasMany(UserBank::class, 'user_id');
    }

    public function userWorks()
    {
        return $this->hasMany(UserWork::class, 'user_id');
    }

    public function userReferences()
    {
        return $this->hasMany(UserReference::class, 'user_id');
    }

    public function userInfos()
    {
        return $this->hasMany(UserInfo::class, 'user_id');
    }

    public function userBranches()
    {
        return $this->belongsToMany(Branch::class, 'user_branches')->withTimestamps();
    }

    public static function validationRules($inputs, $length = 10, $id = 0)
    {
        $rules = [
            'firstname'   => 'required',
            'lastname'    => 'required',
            'email'       => 'required|unique:users,email,' . $id,
            'id_number'   => 'required|unique:users,id_number,' . $id,
            'lang'        => 'required',
            'telephone.*' => 'required|min:' . $length . '|max:' . $length,
        ];

        if (isset($inputs['civil_status']) && $inputs['civil_status'] == 2) {
            $rules += [
                'civil_status'      => 'required|numeric',
                'spouse_first_name' => 'required',
                'spouse_last_name'  => 'required',
            ];
        }
        if ($inputs['role_id'] == '3') {
            $rules += [
                'cellphone.*'       => 'required|min:' . $length . '|max:' . $length,
                'secondary_email.*' => 'required|email',
                'address'           => 'required',
            ];
        }
        if ($inputs['role_id'] != '3') {
            $rules += [
                'branch'   => 'required|array',
                'branch.*' => 'required|numeric',
            ];
        }

        return $rules;
    }

    public static function userValidationRules($inputs)
    {
        $id = $inputs['id'];
        $country = null;
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $country = Country::find(session('country'));
            } else {
                $country = Country::find($inputs['country']);
            }
        } else {
            $country = Country::find(auth()->user()->country);
        }
        $length = $country->phone_length;
        $rules = [
            'firstname'         => 'required',
            'lastname'          => 'required',
            'id_number'         => 'required|unique:users,id_number,' . $id,
            'primary'           => 'required',
            'telephone.*'       => 'required|min:' . $length . '|max:' . $length,
            'secondary_email.*' => [
                'required',
                'email',
                Rule::unique('user_infos', 'value')->ignore($id, 'user_id')->where(function ($query) {
                    $query->where('type', 3);
                }),
            ],
            'commission'        => 'nullable|numeric|min:0.01|max:100',
        ];
        if ($inputs['role_id'] == 3 && isset($inputs['civil_status']) && $inputs['civil_status'] == 2) {
            $rules += [
                'civil_status'      => 'required|numeric',
                'spouse_first_name' => 'required',
                'spouse_last_name'  => 'required',
            ];
        }
        if ($inputs['role_id'] == '3') {
            $rules += [
                'cellphone.*' => 'required|min:' . $length . '|max:' . $length,
                'address'     => 'required',
                'lang'        => 'required',
            ];
        }
        if ($inputs['role_id'] == '2') {
            $rules += [
                'branch'   => 'required|array',
                'branch.*' => 'required|numeric',
            ];
        }

        return $rules;
    }

    public static function merchantValidationRules($inputs)
    {
        $id = $inputs['id'];
        $country = Country::find($inputs['country']);
        $length = $country->phone_length;
        $rules = [
            'firstname'         => 'required',
            'lastname'          => 'required',
            //            'email'             => 'required|unique:users,email,' . $id,
            'telephone.*'       => 'required|min:' . $length . '|max:' . $length,
            'cellphone.*'       => 'required|min:' . $length . '|max:' . $length,
            'primary'           => 'required',
            'secondary_email.*' => [
                'required',
                'email',
                Rule::unique('user_infos', 'value')->ignore($id, 'user_id')->where(function ($query) {
                    $query->where('type', 3);
                }),
            ],
            'address'           => 'required',
            //            'branch'            => 'required|array',
            //            'branch.*'          => 'required|numeric',
        ];
        return $rules;
    }

    public static function registerValidationRules($length = 10)
    {
        $rules = [
            'how_much_loan'      => 'required|numeric',
            'repay_loan_2_weeks' => 'required|in:1,2',
            'have_bank_loan'     => 'required|in:1,2',
            'have_bank_account'  => 'required|in:1,2',
            'firstname'          => 'required',
            'lastname'           => 'required',
            'telephone'          => 'required|min:' . $length . '|max:' . $length,
            'id_number'          => 'required|unique:users,id_number',
            'email'              => [
                'required',
                'email',
                Rule::unique('user_infos', 'value')->where(function ($query) {
                    $query->where('type', 3);
                }),
            ],
            'payslip1'           => 'required',
        ];

        return $rules;
    }

    public static function registerValidationMessages()
    {
        return [
            'firstname.required'          => Lang::get('validation.this_required', ['attribute' => 'Loan amount']),
            'lastname.required'           => Lang::get('validation.this_required', ['attribute' => 'Loan amount']),
            'id_number.required'          => Lang::get('validation.this_required', ['attribute' => 'Loan amount']),
            'id_number.unique'            => Lang::get('validation.already_taken', ['attribute' => 'Loan amount']),
            'email.required'              => Lang::get('validation.this_required', ['attribute' => 'Loan amount']),
            'email.email'                 => Lang::get('validation.should_email', ['attribute' => 'Loan amount']),
            'payslip1.required'           => Lang::get('validation.this_required', ['attribute' => 'Loan amount']),
            'how_much_loan.required'      => Lang::get('validation.this_required', ['attribute' => 'Loan amount']),
            'how_much_loan.numeric'       => Lang::get('validation.should_numeric', ['attribute' => 'Loan amount']),
            'repay_loan_2_weeks.required' => Lang::get('validation.this_required', ['attribute' => 'Repay loan in 2 weeks answer']),
            'have_bank_loan.required'     => Lang::get('validation.this_required', ['attribute' => 'Has bank loan']),
            'have_bank_account.required'  => Lang::get('validation.this_required', ['attribute' => 'Has bank account']),
        ];
    }

    public function userBalance()
    {
        $balance = Wallet::where('user_id', '=', $this->id)->sum('amount');
        return round($balance, 2);
    }

    public function getHoldBalance($id = 0)
    {
        $credits = Credit::where('user_id', '=', $this->id)
            ->where('id', '!=', $id)
            ->whereIn('status', ['1', '2'])
            ->get();
        $credit_amount = $credits->sum('amount');
        $transaction_charges = $credits->sum('transaction_charge');

        return round($credit_amount + $transaction_charges, 2);
    }

    public function createPassword()
    {
        return str_random(6);
    }

    public static function getCivilStatuses()
    {
        $civil_statuses = config('site.civil_statues');

        if (auth()->user()->lang != 'eng') {
            foreach ($civil_statuses as $key => $value) {
                $civil_statuses[$key] = Lang::get('keywords.' . $value, [], auth()->user()->lang);
            }
        }
        return $civil_statuses;
    }

    public function deleteEmail()
    {
        try {
            Mail::to($this->email)->send(new DeleteUserMail($this));
        } catch (\Exception $e) {
            Log::info($e);
        }

        $this->update([
            'deleted_by' => auth()->user()->id,
        ]);

        $status = $this->delete();

        UserReference::where('user_id', '=', $this->id)->update([
            'deleted_by' => $this->id,
        ]);

        UserReference::where('user_id', '=', $this->id)->delete();


        UserBank::where('user_id', '=', $this->id)->update([
            'deleted_by' => $this->id,
        ]);

        UserBank::where('user_id', '=', $this->id)->delete();


        UserInfo::where('user_id', '=', $this->id)->update([
            'deleted_by' => $this->id,
        ]);

        UserInfo::where('user_id', '=', $this->id)->delete();


        UserWork::where('user_id', '=', $this->id)->update([
            'deleted_by' => $this->id,
        ]);

        UserWork::where('user_id', '=', $this->id)->delete();


        Wallet::where('user_id', '=', $this->id)->update([
            'deleted_by' => $this->id,
        ]);

        Wallet::where('user_id', '=', $this->id)->delete();


        $loans = LoanApplication::where('id', $this->id)->get();

        $loans_id = $loans->pluck('id');


        LoanApplication::whereIn('id', $loans_id)->update([
            'deleted_by' => $this->id,
        ]);

        LoanApplication::whereIn('id', $loans_id)->delete();


        LoanStatusHistory::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => $this->id,
        ]);

        LoanStatusHistory::whereIn('loan_id', $loans_id)->delete();


        LoanCalculationHistory::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => $this->id,
        ]);

        LoanCalculationHistory::whereIn('loan_id', $loans_id)->delete();


        LoanNotes::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => $this->id,
        ]);

        LoanNotes::whereIn('loan_id', $loans_id)->delete();


        LoanTransaction::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => $this->id,
        ]);

        LoanTransaction::whereIn('loan_id', $loans_id)->delete();


        return $status;
    }

    public function getReferralCode()
    {
        return strtoupper(substr($this->firstname, 0, 1)) . strtoupper(substr($this->lastname, 0, 1)) . sprintf('%04d', substr($this->id_number, 0, 4)) . sprintf('%04d',
                $this->id);
    }

    public function getStatusReferrals($status = null)
    {
        $client_ids = null;
        if ($status != null) {
            $loan_ids = LoanApplication::select(DB::raw('min(id) as loan_id'), 'client_id')->groupBy('client_id')->pluck('loan_id');
            $client_ids = LoanApplication::whereIn('id', $loan_ids)->where('loan_status', '=', $status)->pluck('client_id');
        }
        $users = User::where('referred_by', '=', $this->referral_code)->whereNotNull('referred_by');
        if ($client_ids != null) {
            $users->whereIn('id', $client_ids);
        }
        return $users->count();
    }

    public static function referralUserWithOneYearLateLoan()
    {
        $except_countries = Country::where('raffle', '!=', 1)->pluck('id');

        $referral_users = User::where('role_id', '=', '3')
            ->whereNotIn('country', $except_countries)
            ->whereNotNull('referred_by')
            ->get();

        $loan_applications = LoanApplication::select(DB::raw('max(date(start_date)) as start_date'), 'client_id')
            ->whereIn('client_id', $referral_users->pluck('id'))
            ->groupBy('client_id')
            ->get();
        $loan_applications = $loan_applications->where('start_date', '!=', null);
        $loan_applications = $loan_applications->where('start_date', '>=', date('Y-m-d', strtotime('-' . config('site.raffle_participation_period') . ' days')));

        return $referral_users->whereIn('id', $loan_applications->pluck('client_id'))->groupBy('country');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->id;
            }
        });

        self::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
        });

        self::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->user()->id;
            }
        });
    }

    public static function getEmployees($country, $get = false)
    {
        $employee = User::select(DB::raw('concat(firstname," ",lastname) as name'), 'id');
        if (auth()->user()->hasRole('super admin') && $country != '') {
            $employee->where('users.country', '=', $country);
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $employee->where('users.country', '=', auth()->user()->country);
            }
        }
        if (auth()->user()->hasAnyRole('admin')) {
            $employee->whereNotIn('role_id', [1]);
        }

        $employee = $employee->whereNotIn('role_id', [3, 4])
            ->orderBy('name', 'asc');

        if ($get) {
            return $employee->get('name', 'id');
        } else {
            return $employee->pluck('name', 'id');
        }
    }

}
