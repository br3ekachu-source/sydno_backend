<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as NotificationsVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends NotificationsVerifyEmail
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject("Подтверждение почты sydno.ru")
            ->line('Мы получили запрос на подтверждение адреса электронной почты.')
            ->line('Нажмите на кнопку ниже, чтобы подтвердить это действие.
            Если вы не запрашивали подтверждение, проигнорируйте это письмо')
            ->action('Подтвердить почту', $url);
    }
}
