<?php

namespace App\Console\Commands;

use App\Library\FirebaseHelper;
use App\Library\Helper;
use App\Models\Country;
use App\Models\RaffleParticipant;
use App\Models\RaffleWinner;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReferralProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral-program:winner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referral Program for assigning winners.';

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
        $date = date('Y-m-d', strtotime('-1 day'));

        $countries = Country::where('raffle', '=', 1)->pluck('id');
        foreach ($countries as $country) {
            $raffle = RaffleWinner::whereDate('date', '=', $date)->where('country_id', '=', $country)->first();
            if ($raffle != null) {
                $participants = RaffleParticipant::where('raffle_id', '=', $raffle->id)
                    ->pluck('user_id')
                    ->toArray();

                $winner = $participants[array_rand($participants, 1)];

                $raffle->update([
                    'user_id' => $winner
                ]);

                $winner = User::where('id', '=', $winner)->first();
                $winner_name = $winner->firstname . ' ' . $winner->lastname;

                $country = Country::find($country);
                foreach ($participants as $key => $value) {
                    $type = 2;
                    if ($value == $winner->id) {
                        $type = 1;
                    }
                    $user = User::find($value);
                    $time = Helper::date_to_current_timezone($raffle->date, $country->timezome, 'M Y');
                    try {
                        Log::info($user);
                        Log::info($type);
                        Log::info($time);
                        Log::info($winner_name);
                        Mail::to($user->email)->send(new \App\Mail\RaffleWinner($user, $type, $time, $winner_name));
                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                    $title = Lang::get('emails.reminder_raffle', ['appname' => config('mail.from.name')], $user->lang);
                    $body = '';
                    if ($type == 1) {
                        $body = Lang::get('emails.winner_raffle', ['appname' => config('mail.from.name'), 'winner_name' => $winner_name, 'time' => $time], $user->lang);
                    } elseif ($type == 2) {
                        $body = Lang::get('emails.winner_raffle_today', ['appname' => config('mail.from.name'), 'winner_name' => $winner_name, 'time' => $time], $user->lang);
                    }
                    $data = [
                        'user_id' => $user->id,
                        'winner_id'
                    ];
                    FirebaseHelper::firebaseNotification($user->id, $title, $body, 'raffle winner', $data);
                }
            }
        }

    }
}
