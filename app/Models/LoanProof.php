<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanProof extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'file_name',
    ];

    public function GetFileURLAttribute()
    {
        return asset('storage/loan_applications/' . $this->id . '/' . $this->file_name);
    }
}
