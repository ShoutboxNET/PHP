<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Shoutbox\Client as ShoutboxClient;
use Shoutbox\EmailOptions;

class ShoutboxMail extends Mailable
{
    use Queueable, SerializesModels;

    private $htmlContent;
    private $attachments = [];

    public function __construct(string $htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    public function build()
    {
        return $this->html($this->htmlContent);
    }

    public function send($mailer)
    {
        // Get Shoutbox API key from Laravel config
        $apiKey = config('services.shoutbox.key');
        $client = new ShoutboxClient($apiKey);

        $options = new EmailOptions();
        $options->from = $this->from[0]['address'];
        $options->name = $this->from[0]['name'] ?? '';
        $options->to = collect($this->to)->pluck('address')->toArray();
        $options->subject = $this->subject;
        $options->html = $this->htmlContent;

        if (!empty($this->replyTo)) {
            $options->replyTo = $this->replyTo[0]['address'];
        }

        // Handle attachments
        if (!empty($this->attachments)) {
            $options->attachments = $this->attachments;
        }

        try {
            $client->sendEmail($options);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to send email via Shoutbox: " . $e->getMessage());
        }
    }
}

/*
// Example usage in Laravel:

// In config/services.php:
'shoutbox' => [
    'key' => env('SHOUTBOX_API_KEY'),
],

// In your controller or other code:
use App\Mail\ShoutboxMail;
use Illuminate\Support\Facades\Mail;

// Simple usage
Mail::to('recipient@example.com')
    ->send(new ShoutboxMail('<h1>Hello</h1><p>This is a test email.</p>'));

// Advanced usage with from address and reply-to
Mail::to('recipient@example.com')
    ->from('sender@example.com', 'Sender Name')
    ->replyTo('reply@example.com')
    ->send(new ShoutboxMail('<h1>Hello</h1><p>This is a test email.</p>'));

// Using in a Job
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Mail::to('recipient@example.com')
            ->send(new ShoutboxMail('<h1>Hello</h1><p>This is a queued email.</p>'));
    }
}
*/
