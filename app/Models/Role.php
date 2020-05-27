<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends \Spatie\Permission\Models\Role
{
    //
    use SoftDeletes;
    protected $fillable = [
    	'name',
    	'name_nl',
    	'name_es',
    	'guard_name',
    ];
}
