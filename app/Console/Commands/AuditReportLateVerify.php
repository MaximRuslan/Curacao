<?php

namespace App\Console\Commands;

use App\Models\Dayopen;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditReportLateVerify extends Command
{
    protected $signature = 'audit-report:late';

    protected $description = 'Didn\'t verify in time frame.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $selection = [
            'dayopens.date',
            'dayopens.user_id',
            'dayopens.branch_id',
            DB::raw('sum(dayopens.amount) as total_amount'),
            DB::raw('concat(users.firstname," ",users.lastname) as user_name'),
            'branches.title as branch_name',
            'countries.name as country_name',
        ];
        $dayopens = Dayopen::select($selection)
            ->leftJoin('users', 'users.id', '=', 'dayopens.user_id')
            ->leftJoin('branches', 'branches.id', '=', 'dayopens.branch_id')
            ->leftJoin('countries', 'countries.id', '=', 'branches.country_id')
            ->whereNull('dayopens.completion_date')
            ->groupBy('dayopens.date', 'dayopens.user_id', 'dayopens.branch_id')
            ->get();

        foreach ($dayopens as $key => $value) {
            $day_end = Dayopen::where('date', '>', $value->date)
                ->where('branch_id', '=', $value->branch_id)
                ->where('user_id', '=', $value->user_id)
                ->orderBy('date', 'asc')
                ->first();
            $value->day_end = null;
            if ($day_end != null) {
                $value->day_end = $day_end->date;
                $value->created_at = $day_end->created_at;
            }
        }
        $dayopens = $dayopens->where('day_end', '!=', null)
            ->where('created_at', '<=', date('Y-m-d H:i:s', strtotime(config('site.late_audit_report_time'))));
        if (count($dayopens) > 0) {
            $admin_emails = User::where('role_id', '=', 1)
                ->where('is_verified', '=', 1)
                ->get();
            foreach ($admin_emails as $key => $value) {
                // Log::info($value->email);
                /*try {
                    Mail::to($value->email)->send(new AuditReportLateMail($value, $dayopens));
                } catch (\Exception $e) {
                    Log::info($e);
                }*/
            }
        }
        echo 'done';
    }
}
