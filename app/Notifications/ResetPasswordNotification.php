<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Mã token đặt lại mật khẩu
     *
     * @var string
     */
    public $token;

    /**
     * Khởi tạo notification với token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Kênh gửi thông báo
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Xây dựng nội dung email đặt lại mật khẩu bằng tiếng Việt
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('🔐 Yêu cầu đặt lại mật khẩu tài khoản PTShop')
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Bạn nhận được email này vì hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản PTShop của bạn.')
            ->action('Đặt lại mật khẩu ngay', $resetUrl)
            ->line('Liên kết đặt lại mật khẩu này sẽ hết hiệu lực sau 60 phút.')
            ->line('Nếu bạn không gửi yêu cầu này, vui lòng bỏ qua email. Mật khẩu của bạn vẫn an toàn tuyệt đối.')
            ->salutation('Trân trọng, Đội ngũ PTShop');
    }
}
