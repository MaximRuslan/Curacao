<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralCategory extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'title',
        'min_referrals',
        'max_referrals',
        'loan_start',
        'loan_pif',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules($max)
    {
        $rules = [
            'title'         => 'required',
            'max_referrals' => 'nullable|numeric',
            'loan_start'    => 'required|numeric',
            'loan_pif'      => 'required|numeric',
            'status'        => 'required|in:0,1',
        ];
        if (auth()->user()->hasRole('super admin') && !session()->has('country')) {
            $rules += [
                'country_id' => 'required|numeric',
            ];
        }
        if ($max != null && $max != '') {
            $rules += [
                'min_referrals' => 'required|numeric|max:' . $max,
            ];
        }
        return $rules;
    }
}
