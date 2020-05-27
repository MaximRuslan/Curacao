<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReference extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'relationship',
        'telephone',
        'cellphone',
        'address',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules($length = 10)
    {
        return [
            'first_name.*'   => 'required',
            'last_name.*'    => 'required',
            'relationship.*' => 'required',
            'telephone.*'    => 'nullable|min:' . $length . '|max:' . $length,
            'cellphone.*'    => 'required|min:' . $length . '|max:' . $length,
            'address.*'      => 'required'
        ];
    }

    public static function validationMessage()
    {
        return [
            'first_name.*.required'   => 'This Field is Required.',
            'last_name.*.required'    => 'This Field is Required.',
            'relationship.*.required' => 'This Field is Required.',
            'telephone.*.required'    => 'This Field is Required.',
            'telephone.*.min'         => 'This Field should be at least :min.',
            'telephone.*.max'         => 'This Field should be no more than :max.',
            'cellphone.*.required'    => 'This Field is Required.',
            'cellphone.*.min'         => 'This Field should be at least :min.',
            'cellphone.*.max'         => 'This Field should be no more than :max.',
            'address.*.required'      => 'This Field is Required.',
        ];
    }
}
