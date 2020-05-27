<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBank extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_number',
        'bank_id',
        'name_on_account',
        'address_on_account',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules()
    {
        return [
            'account_number.*'     => 'required',
            'bank_id.*'            => 'required|numeric',
            'name_on_account.*'    => 'required',
            'address_on_account.*' => 'required',
        ];
    }

    public static function validationMessage()
    {
        return [
            'account_number.*.required'     => 'This Field is required.',
            'bank_id.*.required'            => 'This Field is required.',
            'bank_id.*.numeric'             => 'This Field must be numeric value.',
            'name_on_account.*.required'    => 'This Field is required.',
            'address_on_account.*.required' => 'This Field is required.',
        ];
    }
}
