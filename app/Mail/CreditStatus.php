<?php

namespace App\Mail;

use App\Library\EmailHelper;
use App\Models\Country;
use App\Models\EmailHistory;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class CreditStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $credit;

    public function __construct($user, $credit)
    {
        $this->credit = $credit;
        $this->user = $user;
    }

    public function build()
    {
        $country = Country::find($this->user->country);

        EmailHelper::emailConfigChanges($country->mail_smtp);

        $key = '';

        if ($this->credit->payment_type == 1) {
            $key = 'credit_cash_payout';
        } else if ($this->credit->payment_type == 2) {
            $key = 'credit_bank_transfer';
        }

        $status = '';
        if ($this->credit->status == 1) {
            $status = 'Requested';
        } else if ($this->credit->status == 2) {
            if ($this->credit->payment_type == 2) {
                $status = 'In process';
            } else if ($this->credit->payment_type == 1) {
                $status = 'Approved';
            }
        } else if ($this->credit->status == 3) {
            $status = 'Completed';
        } else if ($this->credit->status == 4) {
            $status = 'Rejected';
        }
        $status = Lang::get('keywords.' . $status, [], $this->user->lang);

        $data = [
            'app_name'  => config('app.name'),
            'user_name' => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'credit_id' => $this->credit->id,
            'status'    => $status,
        ];

        $template = Template::findFromKey($key, 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\CreditStatus',
            'model_id'    => $this->credit->id,
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
