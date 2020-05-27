<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanNotes extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'loan_id',
        'date',
        'follow_up',
        'details',
        'priority',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

}
