<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shoutbox\SMTPClient;
use Shoutbox\EmailOptions;
use Shoutbox\Attachment;

// Example of using Shoutbox SMTP Client
$apiKey = getenv('SHOUTBOX_API_KEY') ?: 'your-api-key-here';
$client = new SMTPClient($apiKey);

try {
    // Basic email
    $options = new EmailOptions();
    $options->from = 'sender@example.com';
    $options->to = 'recipient@example.com';
    $options->subject = 'Test Email via SMTP Client';
    $options->html = '<h1>Hello!</h1><p>This is a test email sent using the Shoutbox SMTP Client.</p>';
    $options->name = 'Sender Name';
    $options->replyTo = 'reply@example.com';

    $client->sendEmail($options);
    echo "Basic email sent successfully!\n";

    // Email with attachment
    $attachment = new Attachment();
    $attachment->filepath = __DIR__ . '/important.txt';
    $attachment->filename = 'important.txt';
    $attachment->contentType = 'text/plain';

    $optionsWithAttachment = new EmailOptions();
    $optionsWithAttachment->from = 'sender@example.com';
    $optionsWithAttachment->to = 'recipient@example.com';
    $optionsWithAttachment->subject = 'Test Email with Attachment via SMTP';
    $optionsWithAttachment->html = '<h1>Hello!</h1><p>This email includes an attachment.</p>';
    $optionsWithAttachment->attachments = [$attachment];

    $client->sendEmail($optionsWithAttachment);
    echo "Email with attachment sent successfully!\n";

    // Email with multiple recipients
    $optionsMultiRecipient = new EmailOptions();
    $optionsMultiRecipient->from = 'sender@example.com';
    $optionsMultiRecipient->to = ['recipient1@example.com', 'recipient2@example.com'];
    $optionsMultiRecipient->subject = 'Test Email with Multiple Recipients';
    $optionsMultiRecipient->html = '<h1>Hello!</h1><p>This email is sent to multiple recipients.</p>';
    $optionsMultiRecipient->headers = [
        'X-Custom-Header' => 'Custom Value',
        'X-Priority' => '1'
    ];

    $client->sendEmail($optionsMultiRecipient);
    echo "Email to multiple recipients sent successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
