<?php

namespace App\Console\Commands;

use App\Events\TimeReceive;
use Illuminate\Console\Command;

class EveryMinuteTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'time-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Time Giving Cron job';

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
        \Log::info('cron job log');
        return event(new TimeReceive());
    }
}
