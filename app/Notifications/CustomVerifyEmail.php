<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class CustomVerifyEmail extends VerifyEmailBase
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('🔐 تأكيد البريد الإلكتروني - ' . config('app.name'))
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('شكراً لك على التسجيل في ' . config('app.name') . '. نحتاج منك تأكيد بريدك الإلكتروني للمتابعة.')
            ->line('يرجى النقر على الزر أدناه لتأكيد بريدك الإلكتروني:')
            ->action('✅ تأكيد البريد الإلكتروني', $verificationUrl)
            ->line('هذا الرابط صالح لمدة ' . Config::get('auth.verification.expire', 60) . ' دقيقة.')
            ->line('إذا لم تقم بإنشاء حساب، فلا داعي لاتخاذ أي إجراء.')
            ->line('ملاحظة: لأسباب أمنية، لن تتمكن من الوصول إلى لوحة التحكم حتى يتم تأكيد بريدك الإلكتروني.')
            ->salutation('مع تحيات فريق ' . config('app.name'));
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
