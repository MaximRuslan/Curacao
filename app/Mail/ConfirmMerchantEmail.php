<?php

namespace App\Mail;

use App\Library\EmailHelper;
use App\Models\EmailHistory;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConfirmMerchantEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $merchant, $id, $email, $password;

    public function __construct($merchant, $id, $email, $password = null)
    {
        $this->merchant = $merchant;
        $this->password = $password;
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

        $key = 'confirm_merchant_password';

        $data = [
            'app_name'          => config('app.name'),
            'merchant_name'     => ucwords(strtolower($this->merchant->first_name . ' ' . $this->merchant->last_name)),
            'verification_link' => route('verify.merchant', ['id' => encrypt($this->merchant->id), 'info' => encrypt($this->id), 'email' => $this->email]),
            'password'          => $this->password,
        ];

        $template = Template::findFromKey($key, 1, $this->merchant->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->merchant->id,
            'email_class' => 'App\Mail\ConfirmMerchantEmail',
            'model_id'    => null,
            'data'        => json_encode([
                'merchant' => $this->merchant,
                'password' => $this->password,
                'id'       => $this->id,
                'email'    => $this->email,
            ]),
        ]);

        $data = [
            'app_name' => config('app.name'),
        ];

        $footer = Template::findFromKey('email_footer', 3, $this->merchant->lang, $data);

        return $this->bcc(config('site.bcc_users'))
            ->markdown('emails.custom')
            ->subject($template->subject)
            ->with([
                'content' => $template->content,
                'footer'  => $footer->content
            ]);
    }
}
