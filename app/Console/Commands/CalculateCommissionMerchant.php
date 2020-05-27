<?php

namespace App\Console\Commands;

use App\Library\Helper;
use App\Models\Country;
use App\Models\LoanTransaction;
use App\Models\Merchant;
use App\Models\MerchantCommission;
use Illuminate\Console\Command;

class CalculateCommissionMerchant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchant:commission {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merchant commission calculation';

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
        $id = $this->argument('id');
        if ($id == 'all') {
            $merchants = Merchant::where('type', '=', 1)->get();
        } else {
            $merchants = Merchant::where('id', '=', $id)->where('type', '=', 1)->get();
        }
        foreach ($merchants as $merchant) {
            $country = Country::find($merchant->country_id);
            if ($country != null) {
                $date = Helper::time_to_current_timezone(date('Y-m-d H:i:s'), $country->timezone, 'Y-m-d H:i:s');
                if (Helper::databaseToFrontEditDate($date, 'd') == 1 || 1) {
                    $month = Helper::databaseToFrontEditDate($date, 'm');
                    $year = Helper::databaseToFrontEditDate($date, 'Y');
                    if ($month == 1) {
                        $month = 12;
                        $year = $year - 1;
                    } else {
                        $month = $month - 1;
                    }
                    $month = 5;

                    $start_date = Helper::currentTimezoneToUtcDateTime($year . '-' . $month . '-01 00:00:00', $country->timezone);
                    $end_date = Helper::currentTimezoneToUtcDateTime($year . '-' . $month . '-31 11:59:59', $country->timezone);

                    $sub_users = Merchant::where('type', '=', 2)->where('merchant_id', '=', $merchant->id)->pluck('id');

                    $loan_transactions = LoanTransaction::where('merchant_id', $merchant->id)
                        ->where('created_at', '>=', $start_date)
                        ->where('created_at', '<=', $end_date)
                        ->where(function ($query) {
                            $query->whereNull('commission_calculated')
                                ->orWhere('commission_calculated', '=', 0);
                        })
                        ->get();


                    $amount = $loan_transactions->sum('amount');

                    $commission = MerchantCommission::where('merchant_id', '=', $merchant->id)
                        ->where('min_amount', '<=', $amount)
                        ->where('max_amount', '>=', $amount)
                        ->first();

                    if ($commission != null) {
                        $commission_value = $amount * $commission->commission / 100;

                        foreach ($loan_transactions as $collected_amount) {
                            $commission_element = $collected_amount->amount * $commission_value / $amount;
                            $collected_amount->update([
                                'commission_calculated' => $commission_element
                            ]);
                        }
                    }
                }
            }
        }
    }
}
