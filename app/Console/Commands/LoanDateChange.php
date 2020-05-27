<?php

namespace App\Console\Commands;

use App\Models\LoanApplication;
use App\Models\LoanStatusHistory;
use Illuminate\Console\Command;

class LoanDateChange extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:dates-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start date and end date on base of loan status history.';

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
        $loans = LoanApplication::all();

        foreach ($loans as $key => $value) {
            echo 'loan_id' . $value->id . PHP_EOL;
            $loan_status = LoanStatusHistory::where('loan_id', '=', $value->id)
                ->where('status_id', '=', 4)
                ->orderBy('created_at', 'asc')
                ->first();

            if ($loan_status != null) {
                $value->update([
                    'start_date' => $loan_status->created_at
                ]);
            }

            $loan_status = LoanStatusHistory::where('loan_id', '=', $value->id)
                ->whereIn('status_id', [7, 8, 9])
                ->orderBy('created_at', 'asc')
                ->first();

            if ($loan_status != null) {
                $value->update([
                    'end_date' => $loan_status->created_at
                ]);
            }
            echo 'loan_id' . $value->id . 'done' . PHP_EOL;
        }
        echo 'Done';
    }
}
