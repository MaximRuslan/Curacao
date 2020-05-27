<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;
use App\Models\LoanApplication;
use App\Models\UserTerritory;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(){
    	$manager = Auth::user();
    	$userCount = User::where(['territory'=>$manager->territory])->where(['role_id'=>'3'])->count();
        $territory = UserTerritory::where(['id'=>$manager->territory])->get();

        $today = Carbon::today()->format('Y-m-d');
        foreach($territory as $item){
            $users = User::where(['territory'=>$item->id])->where(['role_id'=>'3'])->get()->pluck(['id']);
            $applications = LoanApplication::whereIN('client_id',$users)->get();
            $openApplications = LoanApplication::whereIN('client_id',$users)
                                ->whereIN('loan_status',['2'])
                                ->select([
                                    DB::raw("SUM(amount) as balance"),
                                    DB::raw("COUNT(id) as totalApplications")
                                ])
                                ->first();
            $pendingApplications = LoanApplication::whereIN('client_id',$users)->where('loan_status',1)->get();
            $exceeding = LoanApplication::whereIN('client_id',$users)
                         ->where('deadline_date','<',$today)
                         ->where('loan_status','=',1)
                         ->select([
                            DB::raw("SUM(amount) as balance"),
                            DB::raw("COUNT(id) as totalApplications")
                          ])
                          ->first();
            
            $item->userCount = count($users);
            $item->applicationsCount = count($applications);
            $item->pendingCount = count($pendingApplications);
            
            if($openApplications){
                $item->openCount = $openApplications->totalApplications;
                $item->openBalance = $openApplications->balance;    
            }else{
                $item->openCount = 0;
                $item->openBalance = 0;
            }
            if($exceeding){
                $item->exceedingCount = $exceeding->totalApplications;
                $item->exceedingBalance = $exceeding->balance;    
            }else{
                $item->exceedingCount = 0;
                $item->exceedingBalance = 0;
            }
        }
    	$loanCount = LoanApplication::with('user')
					 ->whereHas('user', function($q) use($manager){
					    $q->where('territory', '=',$manager->territory);
					 })
					 ->count();
    	$pendingCount = LoanApplication::where('loan_status',1)
    					->with('user')
						->whereHas('user', function($q) use($manager){
						    $q->where('territory', '=',$manager->territory);
						 })
					 	->count();
    	$data = array(
    		'userCount'=>$userCount,
    		'loanCount'=>$loanCount,
    		'pendingCount'=>$pendingCount,
            'territory'=>$territory,
    	);
    	return view('manager.dashboard',$data);
    }
}
