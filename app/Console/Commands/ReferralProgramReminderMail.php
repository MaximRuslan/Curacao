<?php

namespace App\Console\Commands;

use App\Library\FirebaseHelper;
use App\Mail\RaffleReminder;
use App\Models\RaffleParticipant;
use App\Models\RaffleWinner;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReferralProgramReminderMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral-program:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder mail for referral program.';

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
        $referral_users = User::referralUserWithOneYearLateLoan();

        $timestamp = date('Y-m-d H:i:s');
        foreach ($referral_users as $key => $value) {
            $raffle = RaffleWinner::create([
                'date'       => $timestamp,
                'user_id'    => null,
                'country_id' => $key
            ]);

            foreach ($value as $user) {
                RaffleParticipant::create([
                    'raffle_id' => $raffle->id,
                    'user_id'   => $user->id
                ]);
                try {
                    Mail::to($user->email)->send(new RaffleReminder($user));
                } catch (\Exception $e) {
                    Log::error($e);
                }
                $title = Lang::get('emails.reminder_raffle', ['appname' => config('mail.from.name')], $user->lang);
                $body = Lang::get('emails.reminder_raffle_tomorrow', ['appname' => config('mail.from.name')], $user->lang);
                $data = [
                    'user_id' => $user->id
                ];
                FirebaseHelper::firebaseNotification($user->id, $title, $body, 'raffle reminder', $data);
            }
        }
    }
}
