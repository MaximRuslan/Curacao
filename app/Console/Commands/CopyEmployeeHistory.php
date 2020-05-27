<?php

namespace App\Console\Commands;

use App\Models\LoanApplication;
use App\Models\LoanCalculationHistory;
use Illuminate\Console\Command;

class CopyEmployeeHistory extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy Employee from loan_applications table to loan_calculation_histories table.';

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
        foreach ($loans as $loan) {
            LoanCalculationHistory::where('loan_id', '=', $loan->id)->update([
                'employee_id' => $loan->employee_id,
            ]);
        }
    }

}
