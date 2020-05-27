<?php

namespace App\Mail;

use App\Models\EmailHistory;
use App\Models\ReferralCategory;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $key = 'referral_mail';

        $benefits = ReferralCategory::where('country_id', '=', $this->user->country)->where('status', '=', 1)->get();

        $benefit = '';
        foreach ($benefits as $value) {
            $benefit .= '<tr>
                            <td>' . $value->title . '</td>
                            <td>' . $value->min_referrals . '-' . $value->max_referrals . '</td>
                            <td>' . $value->loan_start . __('keywords.colones', [], $this->user->lang) . '</td>
                            <td>' . $value->loan_pif . __('keywords.colones', [], $this->user->lang) . '</td>
                        </tr>';
        }

        $data = [
            'app_name'      => config('app.name'),
            'client_name'   => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'referral_code' => $this->user->referral_code,
            'benefits_data' => $benefit
        ];

        $template = Template::findFromKey($key, 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\ReferralMail',
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
