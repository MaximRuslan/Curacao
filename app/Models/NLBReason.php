<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NLBReason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
    ];


    public static function validationRules()
    {
        return [
            'type'  => 'required|in:1,2',
            'title' => 'required'
        ];
    }
}
