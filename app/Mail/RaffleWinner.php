<?php

namespace App\Mail;

use App\Models\Country;
use App\Models\EmailHistory;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class RaffleWinner extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user, $type, $time, $winner_name;

    public function __construct($user, $type, $time, $winner_name)
    {
        $this->user = $user;
        $this->type = $type;
        $this->time = $time;
        $this->winner_name = $winner_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $country = Country::find($this->user->country);

        $mobile_no = '';
        $email = '';
        if ($country != null) {
            $mobile_no = $country->telephone;
            $email = $country->email;
        }

        $key = '';
        if ($this->type == 1) {
            $key = 'raffle_winner';
        } elseif ($this->type == 2) {
            $key = 'raffle_looser';
        }

        $data = [
            'app_name'  => config('app.name'),
            'user_name' => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'mobile_no' => $mobile_no,
            'email'     => $email
        ];

        $template = Template::findFromKey($key, 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\RaffleWinner',
            'model_id'    => null,
            'data'        => null,
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
