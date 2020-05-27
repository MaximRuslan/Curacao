<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nlb extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'type',
        'reason',
        'desc',
        'date',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public static function validationRules()
    {
        $rules = [
            'type'   => 'required|in:1,2',
            'reason' => 'required|exists:n_l_b_reasons,id',
        ];
        if (auth()->user()->hasRole('super admin|admin|auditor')) {
            $rules += [
                'date'      => 'required|date_format:d/m/Y',
                'branch_id' => 'required|exists:branches,id'
            ];
            if (!session()->has('country') && auth()->user()->hasRole('super admin')) {
                $rules += [
                    'country_id' => 'required|exists:countries,id'
                ];
            }
        }
        return $rules;
    }
}
