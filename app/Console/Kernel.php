<?php

namespace App\Console;

use App\Console\Commands\AuditReportLateVerify;
use App\Console\Commands\Birthday;
use App\Console\Commands\CalculateCommissionMerchant;
use App\Console\Commands\CopyEmployeeHistory;
use App\Console\Commands\LateCashPayoutApproved;
use App\Console\Commands\LoanCalculation;
use App\Console\Commands\ReferralProgram;
use App\Console\Commands\ReferralProgramReminderMail;
use App\Console\Commands\WebVerifiedDelete;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        LateCashPayoutApproved::class,
        AuditReportLateVerify::class,
        LoanCalculation::class,
        WebVerifiedDelete::class,
        Birthday::class,
        ReferralProgram::class,
        ReferralProgramReminderMail::class,
        CalculateCommissionMerchant::class,
        CopyEmployeeHistory::class,
        // EveryMinuteTransfer::class,
        // AuthPermissionCommand::class,
        // AuthRoleCommand::class,
        // UserToUserInfos::class,
        // LoanDateChange::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (config('site.raffle')) {
            $schedule->command('referral-program:reminder')->dailyAt('16:00');
            $schedule->command('referral-program:winner')->dailyAt('16:00');
        }
//        $schedule->command('merchant:commission all')->dailyAt('16:00');
        $schedule->command('cashpayout-approved:late')->weekdays()->everyMinute();
        $schedule->command('audit-report:late')->weekdays()->everyMinute();

        $schedule->command('backup:clean')->daily()->at('05:00');
        $schedule->command('backup:run')->daily()->at('06:00');
        $schedule->command('loan:calculate before')->dailyAt('03:30');
        if (config('site.cron_auto_mode')) {
            $schedule->command('loan:calculate all')->dailyAt('03:30');
        }
        $schedule->command('loan:calculate after')->dailyAt('03:30');

        $schedule->command('web-verified:delete')->dailyAt('03:30');
        if(env('BIRTHDAY')){
            $schedule->command('birthday')->dailyAt('04:00');
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
