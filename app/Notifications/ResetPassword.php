<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token, $type;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     * @return void
     */
    public function __construct($token, $type = '')
    {
        $this->token = $token;
        $this->type = $type;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.url') . route('password.reset', $this->token, false) . '/' . $notifiable->lang);
        if ($this->type == 'merchant') {
            $url = url(config('app.url') . route('merchant.password.reset', $this->token, false) . '/' . $notifiable->lang);
        }
        return (new MailMessage)
            ->markdown('vendor.notifications.password')
            ->subject(config('mail.from.name') . ': Reset Password.')
            ->bcc(config('site.bcc_users'))
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
