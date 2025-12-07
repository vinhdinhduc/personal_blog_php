<?php

/**
 * Email Helper - Updated with PHPMailer
 * Gửi email cho các chức năng của hệ thống
 */

// Nếu dùng PHPMailer (khuyến nghị)
require_once __DIR__ . '../../../../PHPMailer/Exception.php';
require_once __DIR__ . '../../../../PHPMailer/PHPMailer.php';
require_once __DIR__ . '../../../../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailHelper
{
    private static $config = null;

    /**
     * Load email config
     */
    private static function loadConfig()
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/email.php';
        }
        return self::$config;
    }

    /**
     * Gửi email reset password
     */
    public static function sendPasswordResetEmail($to, $name, $resetLink)
    {
        $subject = 'Đặt lại mật khẩu - BlogIT';
        $message = self::getPasswordResetTemplate($name, $resetLink);

        return self::sendEmail($to, $subject, $message);
    }

    /**
     * Template email reset password
     */
    private static function getPasswordResetTemplate($name, $resetLink)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f7fafc; padding: 30px; }
                .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
                .footer { background: #2d3748; color: #a0aec0; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Đặt lại mật khẩu</h1>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>$name</strong>,</p>
                    
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản BlogIT của bạn.</p>
                    
                    <p>Vui lòng nhấn vào nút bên dưới để đặt lại mật khẩu:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$resetLink' class='button'>Đặt lại mật khẩu</a>
                    </div>
                    
                    <p>Hoặc copy link sau vào trình duyệt:</p>
                    <p style='background: #e2e8f0; padding: 10px; border-radius: 4px; word-break: break-all;'>
                        <a href='$resetLink'>$resetLink</a>
                    </p>
                    
                    <div class='warning'>
                        <strong>⚠️ Lưu ý:</strong>
                        <ul style='margin: 10px 0; padding-left: 20px;'>
                            <li>Link này chỉ có hiệu lực trong <strong>1 giờ</strong></li>
                            <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                            <li>Không chia sẻ link này với bất kỳ ai</li>
                        </ul>
                    </div>
                    
                    <p>Nếu bạn gặp vấn đề, vui lòng liên hệ với chúng tôi.</p>
                    
                    <p>Trân trọng,<br><strong>BlogIT Team</strong></p>
                </div>
                <div class='footer'>
                    <p>Email này được gửi tự động, vui lòng không trả lời.</p>
                    <p>&copy; " . date('Y') . " BlogIT. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Gửi email - Main function
     */
    private static function sendEmail($to, $subject, $message)
    {
        $config = self::loadConfig();

        // Kiểm tra nếu email bị tắt
        if (!$config['enabled']) {
            error_log("Email sending is disabled in config");
            return false;
        }

        // Nếu chỉ log không gửi (để test)
        if ($config['log_only']) {
            return self::logEmail($to, $subject, $message);
        }

        // Chọn method gửi email
        switch ($config['method']) {
            case 'smtp':
                return self::sendViaSMTP($to, $subject, $message);

            case 'sendgrid':
                return self::sendViaSendGrid($to, $subject, $message);

            case 'mailgun':
                return self::sendViaMailgun($to, $subject, $message);

            case 'mail':
            default:
                return self::sendViaPHPMail($to, $subject, $message);
        }
    }

    /**
     * Gửi email qua SMTP (PHPMailer) - KHUYẾN NGHỊ
     */
    private static function sendViaSMTP($to, $subject, $message)
    {
        $config = self::loadConfig();
        $smtp = $config['smtp'];

        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Thêm dòng này
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level: $str");
            };
            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp['host'];
            $mail->SMTPAuth = $smtp['auth'];
            $mail->Username = $smtp['username'];
            $mail->Password = $smtp['password'];
            $mail->SMTPSecure = $smtp['encryption'];
            $mail->Port = $smtp['port'];
            $mail->CharSet = 'UTF-8';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($to);
            $mail->addReplyTo($config['reply_to']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            error_log("Email sent successfully to: $to via SMTP");
            return true;
        } catch (Exception $e) {
            error_log("Email error (SMTP): {$mail->ErrorInfo}");
            error_log("Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email qua PHP mail() - ĐƠN GIẢN NHẤT
     */
    private static function sendViaPHPMail($to, $subject, $message)
    {
        $config = self::loadConfig();

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
            'Reply-To: ' . $config['reply_to'],
            'X-Mailer: PHP/' . phpversion()
        ];

        try {
            $result = mail($to, $subject, $message, implode("\r\n", $headers));

            if ($result) {
                error_log("Email sent successfully to: $to via PHP mail()");
                return true;
            } else {
                error_log("Failed to send email to: $to via PHP mail()");
                return false;
            }
        } catch (Exception $e) {
            error_log("Email error (PHP mail): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi email qua SendGrid API
     */
    private static function sendViaSendGrid($to, $subject, $message)
    {
        $config = self::loadConfig();
        $apiKey = $config['sendgrid']['api_key'];

        $data = [
            'personalizations' => [[
                'to' => [['email' => $to]],
                'subject' => $subject
            ]],
            'from' => [
                'email' => $config['from_email'],
                'name' => $config['from_name']
            ],
            'content' => [[
                'type' => 'text/html',
                'value' => $message
            ]]
        ];

        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 202) {
            error_log("Email sent successfully to: $to via SendGrid");
            return true;
        } else {
            error_log("Failed to send email via SendGrid. Response: $response");
            return false;
        }
    }

    /**
     * Gửi email qua Mailgun API
     */
    private static function sendViaMailgun($to, $subject, $message)
    {
        $config = self::loadConfig();
        $mailgun = $config['mailgun'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://{$mailgun['endpoint']}/v3/{$mailgun['domain']}/messages");
        curl_setopt($ch, CURLOPT_USERPWD, "api:{$mailgun['api_key']}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'from' => "{$config['from_name']} <{$config['from_email']}>",
            'to' => $to,
            'subject' => $subject,
            'html' => $message
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            error_log("Email sent successfully to: $to via Mailgun");
            return true;
        } else {
            error_log("Failed to send email via Mailgun. Response: $response");
            return false;
        }
    }

    /**
     * Log email thay vì gửi (để test)
     */
    private static function logEmail($to, $subject, $message)
    {
        $config = self::loadConfig();
        $logPath = $config['log_path'];

        $logContent = sprintf(
            "[%s] TO: %s | SUBJECT: %s\n%s\n%s\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            str_repeat('-', 80),
            strip_tags($message)
        );

        // Tạo thư mục logs nếu chưa có
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logPath, $logContent, FILE_APPEND);
        error_log("Email logged to file: $to");

        return true;
    }

    /**
     * Gửi email chào mừng
     */
    public static function sendWelcomeEmail($to, $name)
    {
        $subject = 'Chào mừng đến với Blog IT';

        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f7fafc; padding: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Chào mừng đến với BlogIT!</h1>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>$name</strong>,</p>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại BlogIT!</p>
                    <p>Chúc bạn có những trải nghiệm tuyệt vời!</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return self::sendEmail($to, $subject, $message);
    }

    /**
     * Test gửi email
     */
    public static function testEmail($to = 'test@example.com')
    {
        $subject = 'Test Email - BlogIT';
        $message = '<h1>This is a test email</h1><p>If you receive this, email configuration is working!</p>';

        return self::sendEmail($to, $subject, $message);
    }
}
