<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
    ];

    public static function validationRules()
    {
        return [
            "title" => 'required',
        ];
    }
}
