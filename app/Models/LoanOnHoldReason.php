<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanOnHoldReason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_nl',
        'title_es',
    ];

    public static function validationRules()
    {
        return [
            'title'    => 'required',
            'title_nl' => 'required',
            'title_es' => 'required',
        ];
    }
}
