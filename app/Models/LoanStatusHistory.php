<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class LoanStatusHistory extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'status_id',
        'user_id',
        'note',
        'loan_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at'
    ];

    public static function historyHas($loan_id, $status)
    {
        $count = LoanStatusHistory::where('loan_id', '=', $loan_id)->whereIn('status_id', $status)->count();
        if ($count > 0) {
            return true;
        }
        return false;
    }
}
