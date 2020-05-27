<?php

namespace App\Mail;

use App\Models\EmailHistory;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuditReportLateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $data;

    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $key = 'audit_report_late_mail';

        $client_data = '';
        if (count($this->data) > 0) {
            foreach ($this->data as $value) {
                $client_data .= ' <tr>
                                <td>' . \App\Library\Helper::datebaseToFrontDate($value->date) . '</td>
                                <td>' . $value->user_name . '</td>
                                <td>' . $value->branch_name . '</td>
                                <td>' . $value->country_name . '</td>
                                <td>' . $value->total_amount . '</td>
                            </tr>';
            }
        } else {
            $client_data .= '<tr>
                            <td colspan="4">' . __('emails.no_data_found', [], $this->user->lang) . '</td>
                        </tr>';
        }

        $data = [
            'app_name'         => config('app.name'),
            'super_admin_name' => ucwords(strtolower($this->user->firstname . ' ' . $this->user->lastname)),
            'client_data'      => $client_data
        ];

        $template = Template::findFromKey($key, 1, $this->user->lang, $data);

        EmailHistory::create([
            'user_id'     => $this->user->id,
            'email_class' => 'App\Mail\AuditReportLateMail',
            'model_id'    => null,
            'data'        => json_encode($this->data),
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
