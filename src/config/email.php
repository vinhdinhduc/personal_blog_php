<?php

/**
 * Email Configuration
 * Cấu hình email cho hệ thống
 */

return [
    // Email method: 'mail', 'smtp', 'sendgrid', 'mailgun'
    'method' => 'smtp',

    // From email (email gửi đi)
    'from_email' => 'noreply@blogit.com',
    'from_name' => 'BlogIT',

    // Reply to email
    'reply_to' => 'support@blogit.com',
    // SMTP Configuration (dùng cho Gmail, Outlook, etc.)
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => 'vinhdd.k63cntt-a@utb.edu.vn', // Thay bằng email của bạn
        'password' => 'crwkcebftndqyouc', // App Password từ Gmail
        'encryption' => 'tls', // tls hoặc ssl
        'auth' => true,
    ],

    // SendGrid Configuration (nếu dùng SendGrid)
    'sendgrid' => [
        'api_key' => 'your-sendgrid-api-key',
    ],

    // Mailgun Configuration (nếu dùng Mailgun)
    'mailgun' => [
        'api_key' => 'your-mailgun-api-key',
        'domain' => 'your-domain.com',
        'endpoint' => 'api.mailgun.net', // hoặc api.eu.mailgun.net
    ],

    // Email templates path
    'templates_path' => __DIR__ . '/../views/emails/',

    // Enable/disable email sending (để test)
    'enabled' => true,

    // Log emails to file instead of sending (để debug)
    'log_only' => false,
    'log_path' => __DIR__ . '/../logs/emails.log',
];
