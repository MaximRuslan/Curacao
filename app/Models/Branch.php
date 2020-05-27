<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
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
            'country_id' => 'required|numeric',
            'title'      => 'required'
        ];
    }
}
