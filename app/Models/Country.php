<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'country_code',
        'phone_length',
        'logo',
        'valuta_name',
        'tax',
        'tax_percentage',
        'timezone',
        'time_offset',
        'map_link',
        'terms_eng',
        'terms_esp',
        'terms_pap',
        'pagare',
        'telephone',
        'sender_number',
        'email',
        'referral',
        'raffle',
        'decimal',
        'web',
        'company_name',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules($inputs)
    {
        $rules = [
            'name'           => 'required',
            'country_code'   => 'required|numeric',
            'phone_length'   => 'required|numeric',
            "tax"            => 'required',
            "tax_percentage" => 'required|numeric',
            "timezone"       => 'required|in:' . implode(',', timezone_identifiers_list()),
            'map_link'       => 'required',
            'sender_number'  => 'required',
            'telephone'      => 'required',
            'email'          => 'required|email',
            'web'            => 'required',
            'company_name'   => 'required',
        ];

        if ($inputs['id'] == '' || $inputs['id'] == null) {
            $rules += [
                "logo" => 'required',
            ];
        }

        return $rules;
    }

    public static function pluckListing($user, $country = '')
    {
        $countries = collect([]);

        if (!$user->hasRole('super admin')) {
            $countries = self::orderBy('name', 'asc')
                ->where('id', '=', $user->country)
                ->pluck('name', 'id');
        } else if ($user->hasRole('super admin') && $country != '') {
            $countries = self::orderBy('name', 'asc')->where('id', '=', $country)->pluck('name', 'id');
        } else {
            $countries = self::orderBy('name', 'asc')->pluck('name', 'id');
        }
        return $countries->map(function ($item, $key) {
            return ucwords(strtolower($item));
        });
    }
}
