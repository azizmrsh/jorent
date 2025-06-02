<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class TenantVerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('🏠 تأكيد البريد الإلكتروني - نظام إدارة الإيجارات')
            ->greeting('مرحباً ' . $notifiable->firstname . ' ' . $notifiable->lastname . '!')
            ->line('🎉 مرحباً بك في نظام إدارة الإيجارات!')
            ->line('📧 يرجى تأكيد عنوان بريدك الإلكتروني بالنقر على الزر أدناه:')
            ->action('✅ تأكيد البريد الإلكتروني', $verificationUrl)
            ->line('🔒 إذا لم تقم بإنشاء حساب، فلا حاجة لاتخاذ أي إجراء.')
            ->line('⏰ هذا الرابط صالح لمدة 60 دقيقة فقط.')
            ->line('💼 شكراً لاستخدامك نظام إدارة الإيجارات!')
            ->salutation('مع أطيب التحيات،' . "\n" . 'فريق نظام إدارة الإيجارات');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'tenant.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
