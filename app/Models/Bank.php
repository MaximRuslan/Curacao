<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'transaction_fee_type',
        'transaction_fee',
        'tax_transaction',
        'country_id',
    ];

    public static function validationRules()
    {
        return [
            "name"            => 'required',
            "country_id"      => 'required|numeric',
            'contact_person'  => 'required',
            'email'           => 'required|email',
            'phone'           => 'required|numeric',
            'transaction_fee' => 'required|numeric',
            'tax_transaction' => 'required|numeric'
        ];
    }
}
