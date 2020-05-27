<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTerritory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_nl',
        'title_es',
        'country_id'
    ];

    public function Users()
    {
        return $this->hasMany(User::class, 'territory', 'id');
    }

    public static function validationRules()
    {
        return [
            'title'      => 'required',
            'country_id' => 'required|numeric',
        ];
    }
}
