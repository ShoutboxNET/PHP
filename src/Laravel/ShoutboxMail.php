<?php

namespace Shoutbox\Laravel;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Shoutbox\SMTPClient;
use Shoutbox\EmailOptions;

class ShoutboxMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $htmlContent;
    public $attachments = [];
    private ?string $apiKey = null;

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
        // Use injected API key for testing, otherwise use config
        $apiKey = $this->apiKey ?? config('services.shoutbox.key');
        if (empty($apiKey)) {
            throw new \RuntimeException('Shoutbox API key not configured');
        }

        $client = new SMTPClient($apiKey);

        $options = new EmailOptions();

        // Handle From
        if (empty($this->from)) {
            throw new \RuntimeException('From address is required');
        }
        $options->from = $this->from[0]['address'];
        if (!empty($this->from[0]['name'])) {
            $options->name = $this->from[0]['name'];
        }

        // Handle To
        if (empty($this->to)) {
            throw new \RuntimeException('To address is required');
        }
        $toAddresses = collect($this->to)->pluck('address')->toArray();
        $options->setTo($toAddresses);

        // Handle Subject
        if (empty($this->subject)) {
            throw new \RuntimeException('Subject is required');
        }
        $options->subject = $this->subject;

        // Set HTML content
        $options->html = $this->htmlContent;

        // Handle Reply-To
        if (!empty($this->replyTo)) {
            $options->replyTo = $this->replyTo[0]['address'];
        }

        // Handle attachments
        if (!empty($this->attachments)) {
            $options->attachments = [];
            foreach ($this->attachments as $attachment) {
                $attachmentObj = new \Shoutbox\Attachment();
                if (!empty($attachment['file'])) {
                    $attachmentObj->filepath = $attachment['file'];
                    $attachmentObj->filename = $attachment['options']['as'] ?? basename($attachment['file']);
                    $attachmentObj->contentType = $attachment['options']['mime'] ?? mime_content_type($attachment['file']);
                    $options->attachments[] = $attachmentObj;
                }
            }
        }

        try {
            $client->sendEmail($options);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to send email: ' . $e->getMessage());
        }
    }

    public function attach($file, array $options = [])
    {
        $this->attachments[] = compact('file', 'options');
        return $this;
    }
}
