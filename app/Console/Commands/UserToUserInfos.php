<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Console\Command;

class UserToUserInfos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-user-infos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'users to users infos emails transfer';

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
        $users = User::get();

        foreach ($users as $key => $value) {
            $is_verified = 0;
            if ($value->is_verified == 1) {
                $is_verified = 1;
            }
            $usersInfo = UserInfo::updateOrcreate([
                'user_id' => $value->id,
                'type'    => 3,
                'value'   => $value->email,
            ], [
                'is_verified' => $is_verified,
                'primary'     => 1,
                'send_mail'   => 1
            ]);
        }
        echo 'done';
    }
}
