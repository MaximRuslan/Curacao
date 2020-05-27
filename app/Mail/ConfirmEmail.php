<?php

namespace App\Mail;

use App\Library\EmailHelper;
use App\Models\EmailHistory;
use App\Models\Template;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user, $type, $id, $email, $password;

    public function __construct($user, $type, $id, $email, $password = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->type = $type;
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        EmailHelper::emailConfigChanges('user');

        $key = '';
        if ($this->type == 'web') {
            $key = 'confirm_web_email';
            if ($this->password != null) {
                $key = 'confirm_web_email_password';
            }
        } else {
            $key = 'confirm_email';
            if ($this->password != null) {
                $key = 'confirm_email_password';
            }
        }

        $referred_by_name = '';
        if ($this->user->referred_by != null) {
            $referred_by = User::where('referral_code', '=', $this->user->referred_by)->first();
            $referred_by_name = $referred_by->firstname . ' ' . $referred_by->lastname;
        }

        $data = [
            'app_name'          => config('app.name'),
            'user_name'         => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'verification_link' => route('verify.client', ['id' => encrypt($this->user->id), 'info' => encrypt($this->id), 'email' => $this->email]),
            'password'          => $this->password,
            'referred_by_name'  => $referred_by_name
        ];

        $template = Template::findFromKey($key, 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\ConfirmEmail',
            'model_id'    => null,
            'data'        => json_encode([
                'user'     => $this->user,
                'password' => $this->password,
                'type'     => $this->type,
                'id'       => $this->id,
                'email'    => $this->email,
            ]),
        ]);

        $data = [
            'app_name' => config('app.name'),
        ];

        $footer = Template::findFromKey('email_footer', 3, $this->user->lang, $data);

        return $this->bcc(config('site.bcc_users'))
            ->markdown('emails.custom')
            ->subject($template->subject)
            ->with([
                'content' => $template->content,
                'footer'  => $footer->content
            ]);
    }
}
