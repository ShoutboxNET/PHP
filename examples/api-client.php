<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shoutbox\Client;
use Shoutbox\EmailOptions;
use Shoutbox\Attachment;

// Example of using Shoutbox API Client
$apiKey = getenv('SHOUTBOX_API_KEY') ?: 'your-api-key-here';
$client = new Client($apiKey);

try {
    // Basic email
    $options = new EmailOptions();
    $options->from = 'sender@example.com';
    $options->to = 'recipient@example.com';
    $options->subject = 'Test Email via API Client';
    $options->html = '<h1>Hello!</h1><p>This is a test email sent using the Shoutbox API Client.</p>';
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
    $optionsWithAttachment->subject = 'Test Email with Attachment';
    $optionsWithAttachment->html = '<h1>Hello!</h1><p>This email includes an attachment.</p>';
    $optionsWithAttachment->attachments = [$attachment];

    $client->sendEmail($optionsWithAttachment);
    echo "Email with attachment sent successfully!\n";

    // Email with custom headers
    $optionsWithHeaders = new EmailOptions();
    $optionsWithHeaders->from = 'sender@example.com';
    $optionsWithHeaders->to = 'recipient@example.com';
    $optionsWithHeaders->subject = 'Test Email with Custom Headers';
    $optionsWithHeaders->html = '<h1>Hello!</h1><p>This email includes custom headers.</p>';
    $optionsWithHeaders->headers = [
        'X-Custom-Header' => 'Custom Value',
        'X-Priority' => '1'
    ];

    $client->sendEmail($optionsWithHeaders);
    echo "Email with custom headers sent successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
