<?php

namespace App\Console\Commands;

use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use App\Models\LoanNotes;
use App\Models\LoanStatusHistory;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Models\UserReference;
use App\Models\UserBank;
use App\Models\UserInfo;
use App\Models\UserWork;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WebVerifiedDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web-verified:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Web registred but not verified not delete users.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Web verified delete cron started.');

        $users_ids = User::whereNull('web_registered')->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-' . config('site.web_verified_delete_hours') . ' hours')))->pluck('id');

        Log::info($users_ids);

        $files = User::whereIn('id', $users_ids)->pluck('payslip1');

        foreach ($files as $file) {
            File::delete(public_path('uploads/' . $file));
        }
        User::whereIn('id', $users_ids)->update([
            'deleted_by' => 0
        ]);
        User::whereIn('id', $users_ids)->delete();


        UserReference::whereIn('user_id', $users_ids)->update([
            'deleted_by' => 0
        ]);

        UserReference::whereIn('user_id', $users_ids)->delete();


        UserBank::whereIn('user_id', $users_ids)->update([
            'deleted_by' => 0
        ]);
        UserBank::whereIn('user_id', $users_ids)->delete();

        UserInfo::whereIn('user_id', $users_ids)->update([
            'deleted_by' => 0
        ]);
        UserInfo::whereIn('user_id', $users_ids)->delete();

        UserWork::whereIn('user_id', $users_ids)->update([
            'deleted_by' => 0
        ]);
        UserWork::whereIn('user_id', $users_ids)->delete();

        Wallet::whereIn('user_id', $users_ids)->update([
            'deleted_by' => 0
        ]);
        Wallet::whereIn('user_id', $users_ids)->delete();

        $loans = LoanApplication::whereIn('client_id', $users_ids)->get();

        $loans_id = $loans->pluck('id');

        LoanApplication::whereIn('id', $loans_id)->update([
            'deleted_by' => 0
        ]);
        LoanApplication::whereIn('id', $loans_id)->delete();


        LoanStatusHistory::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => 0
        ]);
        LoanStatusHistory::whereIn('loan_id', $loans_id)->delete();


        LoanCalculationHistory::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => 0
        ]);
        LoanCalculationHistory::whereIn('loan_id', $loans_id)->delete();


        LoanNotes::whereIn('loan_id', $loans_id)->update([
            'deleted_by' => 0
        ]);
        LoanNotes::whereIn('loan_id', $loans_id)->delete();

        LoanTransaction::whereIn('loan_id', $loans_id)->delete();

        Log::info('Web verified delete cron ended.');
    }
}
