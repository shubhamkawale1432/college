<?php
declare(strict_types=1);

namespace Services;

use Core\Logger;

final class MailerService
{
    public static function send(string $to, string $subject, string $bodyHtml, string $bodyText = ''): bool
    {
        $fromEmail = setting('institute_email', config('mail_from', 'no-reply@college.local'));
        $fromName = setting('institute_name', config('name', 'College Management System'));

        $logEntry = sprintf(
            "[%s] EMAIL TO: %s | SUBJECT: %s\n%s\n----------------------------------------\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $bodyText ?: strip_tags($bodyHtml)
        );

        $mailLog = dirname(__DIR__) . '/storage/logs/mail.log';
        file_put_contents($mailLog, $logEntry, FILE_APPEND | LOCK_EX);

        // Try standard PHP mail if environment permits
        if (ini_get('sendmail_path') || ini_get('SMTP')) {
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=utf-8',
                "From: {$fromName} <{$fromEmail}>",
                "Reply-To: {$fromEmail}",
                'X-Mailer: PHP/' . phpversion(),
            ];
            @mail($to, $subject, $bodyHtml, implode("\r\n", $headers));
        }

        return true;
    }

    public static function sendPasswordReset(string $to, string $name, string $resetUrl): bool
    {
        $institute = setting('institute_name', 'College Management System');
        $subject = "Password Reset Request - {$institute}";
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                <h2 style='color: #4f46e5;'>{$institute}</h2>
                <p>Hello <strong>{$name}</strong>,</p>
                <p>We received a request to reset your account password. Click the button below to set a new password:</p>
                <p style='margin: 24px 0;'>
                    <a href='{$resetUrl}' style='background: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Reset Password</a>
                </p>
                <p style='color: #64748b; font-size: 13px;'>If you did not request this, you can safely ignore this email. This link will expire in 1 hour.</p>
            </div>
        ";
        return self::send($to, $subject, $html);
    }
}
