<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dayopen extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'date',
        'payment_type',
        'amount',
        'custom_created_at',
    ];

    public static function validationRules()
    {
        $rules = [
            'amount'   => 'required|array',
            'amount.*' => 'nullable|numeric',
            'type'     => 'required',
            'date'     => 'required',
        ];

        if (auth()->user()->hasRole('super admin|admin')) {
            $rules += [
                'branch_id' => 'required',
            ];
            if (auth()->user()->hasRole('super admin') && !session()->has('country')) {
                $rules += [
                    'country_id' => 'required',
                ];
            }
        }

        return $rules;
    }
}
