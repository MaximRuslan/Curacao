<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSalaryPercentage extends Model
{
    //
    protected $table = 'loan_salary_percentage';
    protected $fillable = [
        'default_percentage',
    ];
}
