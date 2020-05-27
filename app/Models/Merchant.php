<?php

namespace App\Models;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Merchant extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'merchant_id',
        'branch_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'telephone',
        'tax_id',
        'lang',
        'country_id',
        'is_verified',
        'status',
        'last_activity',
        'reconciliation',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token, 'merchant'));
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
//                $model->deleted_by = auth()->user()->id;
            }
        });
    }


    public static function pluckListing($type = 1, $user = null)
    {
        $merchants = self::select('*', DB::raw('concat(first_name," ",last_name) as username'));
        if ($user != null) {
            if ($user->hasRole('super admin')) {
                if (session()->has('country')) {
                    $merchants->where('country_id', '=', session('country'));
                }
            } else {
                $merchants->where('country_id', '=', $user->country);
            }
        }
        if ($type == 1) {
            $merchants = $merchants->where('type', '=', $type)->orderBy('name', 'asc')->pluck('name', 'id');
        } else if ($type == 2) {
            $merchants = $merchants->where('type', '=', $type)->orderBy('first_name', 'asc')->pluck('username', 'id');
        }
        return $merchants->map(function ($item, $key) {
            return ucwords(strtolower($item));
        });
    }

    public static function validationRules($inputs, $user, $id = null)
    {
        $country = null;
        if (auth()->user()->hasRole('super admin')) {
            if (session()->has('country')) {
                $country = Country::find(session('country'));
            } else {
                $country = Country::find($inputs['country_id']);
            }
        } else {
            $country = Country::find(auth()->user()->country);
        }
        $rules = [
            'type'       => 'required',
            'first_name' => 'required',
            'last_name'  => 'required',
            'lang'       => 'required',
            'status'     => 'required',
        ];
        $type = null;
        if (isset($inputs['type'])) {
            $type = $inputs['type'];
        }
        if ($type == 1) {
            $rules += [
                'tax_id'            => 'required',
                'primary'           => 'required',
                'country_id'        => 'required',
                'secondary_email'   => 'required|array|min:1',
                'secondary_email.*' => [
                    'required',
                    'email',
                    Rule::unique('merchant_details', 'value')->ignore($id, 'merchant_id')->where(function ($query) {
                        $query->where('type', 1);
                    })
                ],
                'branches'          => 'required|array|min:1',
                'branches.*'        => [
                    'required'
                ],
                'telephone'         => 'required|array|min:1',
                'telephone.*'       => [
                    'required',
                    'numeric',
                    Rule::unique('merchant_details', 'value')->ignore($id, 'merchant_id')->where(function ($query) {
                        $query->where('type', 2);
                    })
                ],
                'min_amount'        => 'required|array|min:1',
                'min_amount.*'      => [
                    'required',
                    'numeric',
                ],
                'max_amount'        => 'required|array|min:1',
                'max_amount.*'      => [
                    'required',
                    'numeric',
                ],
                'commission'        => 'required|array|min:1',
                'commission.*'      => [
                    'required',
                    'numeric',
                ],

            ];
        } else if ($type == 2) {
            $rules += [
                'email'       => [
                    'required',
                    'email',
                    Rule::unique('merchant_details', 'value')->ignore($id, 'merchant_id')->where(function ($query) {
                        $query->where('type', 1);
                    })
                ],
                'branch_id'   => 'required',
                'merchant_id' => 'required'
            ];
        }
        return $rules;
    }

    public static function validationMessages($inputs)
    {
        $messages = [
            'secondary_email.required' => 'Minimum one email is required.',
            'secondary_email.array'    => 'Minimum one email is required.',
            'secondary_email.min'      => 'Minimum one email is required.',
            'telephone.required'       => 'Minimum one telephone is required.',
            'telephone.array'          => 'Minimum one telephone is required.',
            'telephone.min'            => 'Minimum one telephone is required.',
            'branches.required'        => 'Minimum one branch is required.',
            'branches.array'           => 'Minimum one branch is required.',
            'branches.min'             => 'Minimum one branch is required.',
            'primary.required'         => 'Minimum one primary email is required.',
            'min_amount.required'      => 'Minimum one comission criteria required.',
        ];
        return $messages;
    }

    public static function commissionValidation($min_amounts, $max_amounts)
    {
        $errors = [];
        $max = 0;
        foreach ($min_amounts as $key => $value) {
            $min = $value;
            if ($min <= $max) {
                $errors['min_amount.' . $key] = ['Min Amount should be greater than ' . $max];
            }
            $max = $max_amounts[$key];
        }
        return $errors;
    }

    public function saveCommission($min_amounts, $max_amounts, $commissions, $commission_id)
    {
        MerchantCommission::where('merchant_id', '=', $this->id)
            ->whereNotIn('id', $commission_id)
            ->delete();

        foreach ($min_amounts as $key => $value) {
            $commission = MerchantCommission::find($commission_id[$key]);
            if ($commission != null) {
                $commission->update([
                    'min_amount' => $value,
                    'max_amount' => $max_amounts[$key],
                    'commission' => $commissions[$key],
                ]);
            } else {
                MerchantCommission::create([
                    'merchant_id' => $this->id,
                    'min_amount'  => $value,
                    'max_amount'  => $max_amounts[$key],
                    'commission'  => $commissions[$key],
                ]);
            }
        }
    }

    public function saveEmails($emails, $email_ids, $primary)
    {
        MerchantDetail::where('merchant_id', '=', $this->id)
            ->where('type', '=', 1)
            ->whereNotIn('id', $email_ids)
            ->delete();

        foreach ($emails as $key => $value) {
            $primary_value = 0;
            if ($primary == $key) {
                $primary_value = 1;
            }
            $email = MerchantDetail::find($email_ids[$key]);
            if ($email != null) {
                $save_again = false;
                if ($email->value != $value) {
                    $save_again = true;
                }
                $email->update([
                    'value'   => $value,
                    'primary' => $primary_value,
                ]);
                if ($save_again) {
                    $email->update([
                        'is_verified' => 0
                    ]);
                }
            } else {
                MerchantDetail::create([
                    'merchant_id' => $this->id,
                    'type'        => 1,
                    'value'       => $value,
                    'primary'     => $primary_value,
                ]);
            }
            if ($primary_value == 1) {
                $this->update([
                    'email' => $value
                ]);
            }
        }
    }

    public function saveBranches($branches, $branch_ids)
    {
        MerchantBranch::where('merchant_id', '=', $this->id)
            ->whereNotIn('id', $branch_ids)
            ->delete();

        foreach ($branches as $key => $value) {
            $branch = MerchantBranch::find($branch_ids[$key]);
            if ($branch != null) {
                $branch->update([
                    'name' => $value
                ]);
            } else {
                MerchantBranch::create([
                    'merchant_id' => $this->id,
                    'name'        => $value,
                ]);
            }
        }
    }

    public function saveTelephone($telephones, $telephone_id, $primary)
    {
        MerchantDetail::where('merchant_id', '=', $this->id)
            ->where('type', '=', 2)
            ->whereNotIn('id', $telephone_id)
            ->delete();

        foreach ($telephones as $key => $value) {
            $primary_value = 0;
            if ($primary == $key) {
                $primary_value = 1;
            }
            $telephone = MerchantDetail::find($telephone_id[$key]);
            if ($telephone != null) {
                $telephone->update([
                    'value'   => $value,
                    'primary' => $primary_value,
                ]);
            } else {
                MerchantDetail::create([
                    'merchant_id' => $this->id,
                    'type'        => 2,
                    'value'       => $value,
                    'primary'     => $primary_value,
                ]);
            }
            if ($primary_value == 1) {
                $this->update([
                    'telephone' => $value
                ]);
            }
        }
    }

    public function createPassword()
    {
        return str_random(6);
    }
}
