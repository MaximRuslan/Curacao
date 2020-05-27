<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWork extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'employer',
        'address',
        'telephone',
        'extension',
        'cellphone',
        'position',
        'employed_since',
        'employment_type',
        'contract_expires',
        'department',
        'supervisor_telephone_code',
        'telephone_code',
        'cellphone_code',
        'supervisor_name',
        'salary',
        'payment_frequency',
        'supervisor_telephone',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules()
    {
        return [
            'employer'          => 'required',
            'address'           => 'required',
            'telephone'         => 'required|numeric',
            'position'          => 'required',
            'employed_since'    => 'required',
            'employment_type'   => 'required|numeric',
            'salary'            => 'required|numeric',
            'payment_frequency' => 'required|numeric'
        ];
    }
}
