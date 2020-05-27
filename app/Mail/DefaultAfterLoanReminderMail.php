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

class DefaultAfterLoanReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $loan, $outstanding_balance;

    public function __construct($user, $loan, $outstanding_balance)
    {
        $this->user = $user;
        $this->loan = $loan;
        $this->outstanding_balance = $outstanding_balance;
    }

    public function build()
    {
        $country = Country::find($this->user->country);

        EmailHelper::emailConfigChanges($country->mail_smtp);

        $data = [
            'app_name'            => config('app.name'),
            'client_name'         => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'loan_id'             => $this->loan->id,
            'outstanding_balance' => $this->outstanding_balance
        ];

        $template = Template::findFromKey('loan_default_after_reminder_mail', 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\DefaultAfterLoanReminderMail',
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
