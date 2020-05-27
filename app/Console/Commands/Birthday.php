<?php

namespace App\Console\Commands;

use App\Library\FirebaseHelper;
use App\Models\Template;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Birthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Birthday Mail and push messages';

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
        $users = User::where('role_id', '=', 3)
            ->whereDay('dob', '=', date('d'))
            ->whereMonth('dob', '=', date('m'))
            ->where('web_registered', '=', 1)
            ->get();

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new \App\Mail\Birthday($user));
            } catch (\Exception $e) {
                Log::error($e);
            }
            $data = [
                'app_name'    => config('app.name'),
                'client_name' => ucwords(strtolower($user->firstname . ' ' . $user->lastname)),
            ];

            $key = 'birthday_message';

            $template = Template::findFromKey($key, 2, $user->lang, $data);

            $data = [];

            FirebaseHelper::firebaseNotification($user->id, $template->subject, $template->content, 'birthday', $data);
        }
    }
}
