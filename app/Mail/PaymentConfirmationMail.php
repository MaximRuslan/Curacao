<?php

namespace App\Mail;

use App\Library\EmailHelper;
use App\Models\Country;
use App\Models\EmailHistory;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user, $loan, $receipt;

    public function __construct($user, $loan, $receipt)
    {
        $this->user = $user;
        $this->loan = $loan;
        $this->receipt = $receipt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $country = Country::find($this->user->country);

        EmailHelper::emailConfigChanges($country->mail_smtp);

        $data = [
            'app_name'     => config('app.name'),
            'client_name'  => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'receipt_link' => $this->receipt
        ];

        $template = Template::findFromKey('payment_confirmation_mail', 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\PaymentConfirmationMail',
            'model_id'    => $this->loan->id,
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
