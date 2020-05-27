<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanStatus extends Model
{

    use SoftDeletes;

    protected $table = 'loan_status';

    protected $fillable = [
        'title',
        'title_nl',
        'title_es',
    ];

    public static function userWiseStatus($user)
    {
        $statuses = [];
        if ($user->hasRole('processor')) {
            $statuses = [4, 5, 6, 7, 8, 9, 10, 11, 12];
        } else if ($user->hasRole('auditor')) {
            $statuses = [];
        } else if ($user->hasRole('debt collector')) {
            $statuses = [6, 9];
        } else if ($user->hasRole('loan approval')) {
            $statuses = [1, 2, 3, 4, 11, 12];
        } else if ($user->hasRole('credit and processing')) {
            $statuses = [3, 4, 11, 12];
        }
        return $statuses;
    }

}
