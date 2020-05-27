<?php

namespace App\Mail;

use App\Library\EmailHelper;
use App\Models\Country;
use App\Models\EmailHistory;
use App\Models\LoanDeclineReason;
use App\Models\LoanOnHoldReason;
use App\Models\LoanReason;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user, $loan;

    public function __construct($user, $loan)
    {
        $this->user = $user;
        $this->loan = $loan;
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

        $key = 'loan_status';

        $pagare_link = '';

        if ($this->loan->loan_status == 2 || $this->loan->loan_status == 11) {
            $key = 'loan_on_hold_declined_status';
        } else if ($this->loan->loan_status == 4) {
            $key = 'loan_current_status';
            $pagare_link = $this->loan->createCountryPdf();
        } else if ($this->loan->loan_status == 12) {
            $key = 'loan_pre_approved_status';
        }
        $reason = LoanReason::find($this->loan->loan_reason);

        if ($reason != null) {
            $this->loan->reason = $reason->title;
        }

        $loan_status = \App\Models\LoanStatus::find($this->loan->loan_status);

        if ($loan_status != null) {
            if ($this->user->lang == "esp") {
                $this->loan->status = $loan_status->title_es;
            } else if ($this->user->lang == "pap") {
                $this->loan->status = $loan_status->title_nl;
            } else {
                $this->loan->status = $loan_status->title;
            }
        }
        $reason = '';
        if ($this->loan->loan_status == 11) {
            $decline_reason = LoanDeclineReason::find($this->loan->loan_decline_reason);
            if ($decline_reason != null) {
                $reason = $decline_reason->title;
            }
        }
        if ($this->loan->loan_status == 2) {
            $decline_reason = LoanOnHoldReason::find($this->loan->loan_decline_reason);
            if ($decline_reason != null) {
                $reason = $decline_reason->title;
            }
        }

        $data = [
            'app_name'    => config('app.name'),
            'user_name'   => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'loan_id'     => $this->loan->id,
            'loan_reason' => $this->loan->reason,
            'status'      => $this->loan->status,
            'reason'      => $reason,
            'description' => $this->loan->decline_description,
            'pagare_link' => $pagare_link
        ];

        $template = Template::findFromKey($key, 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\LoanStatus',
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
