<?php

namespace App\Console\Commands;

use App\Events\LateCashPayApproval;
use App\Models\Credit;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LateCashPayoutApproved extends Command
{
    protected $signature = 'cashpayout-approved:late';

    protected $description = 'Cash Payout Approved Late';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $credits = Credit::where('payment_type', '=', 1)
            ->where('status', '=', 2)
            ->where('updated_at', '<', date('Y-m-d H:i:s', strtotime(config('site.late_cash_payout_time'))))
            ->get();
        $branches = $credits->pluck('branch_id');
        $users = User::select('users.*', DB::raw('group_concat(user_branches.branch_id) as branch_id'))
            ->leftJoin('user_branches', 'user_branches.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 9)
            ->whereIn('user_branches.branch_id', $branches)
            ->where('users.is_verified', '=', 1)
            ->groupBy('users.id')
            ->get();
        foreach ($users as $key => $value) {
            $parts = explode(',', $value->branch_id);
            foreach ($parts as $branch) {
                event(new LateCashPayApproval($value, $branch));
            }
        }
    }
}
