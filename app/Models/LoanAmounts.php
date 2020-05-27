<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LoanProof;

class LoanAmounts extends Model
{
    //
    protected $fillable = [
    	'loan_id',
    	'attachment_id',
    	'type',
    	'amount',
    	'amount_type',
    	'date',
    ];    

    public function documents(){
        return $this->hasOne(LoanProof::class,'id','attachment_id');
    }

}
