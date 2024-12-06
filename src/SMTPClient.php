<?php

namespace Shoutbox;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class SMTPClient
{
    private string $apiKey;
    private PHPMailer $mailer;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->mailer = new PHPMailer(true);
        
        // Enable debug mode
        $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        
        // Configure SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'mail.shoutbox.net';
        $this->mailer->Port = 587;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'shoutbox';
        $this->mailer->Password = $apiKey;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
        // Additional SMTP settings
        $this->mailer->SMTPKeepAlive = true; // Keep the SMTP connection alive
        $this->mailer->Timeout = 60; // Increase timeout
        $this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $this->mailer->Encoding = PHPMailer::ENCODING_BASE64;
    }

    /**
     * @throws Exception
     */
    public function sendEmail(EmailOptions $options): void
    {
        try {
            // Reset all recipients and attachments
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();

            // Set sender
            $this->mailer->setFrom($options->from, $options->name ?? '');

            if (!empty($options->replyTo)) {
                $this->mailer->addReplyTo($options->replyTo);
            }

            // Add recipients
            $recipients = is_array($options->to) ? $options->to : [$options->to];
            foreach ($recipients as $recipient) {
                $this->mailer->addAddress(trim($recipient)); // Trim any whitespace
            }

            // Set subject and body
            $this->mailer->Subject = $options->subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $options->html;
            // Add plain text version
            $this->mailer->AltBody = strip_tags($options->html);

            // Add custom headers
            if (!empty($options->headers)) {
                foreach ($options->headers as $name => $value) {
                    $this->mailer->addCustomHeader($name, $value);
                }
            }

            // Add attachments
            if (!empty($options->attachments)) {
                foreach ($options->attachments as $attachment) {
                    if ($attachment->content) {
                        $this->mailer->addStringAttachment(
                            base64_decode($attachment->content),
                            $attachment->filename,
                            'base64',
                            $attachment->contentType
                        );
                    } else {
                        $this->mailer->addAttachment(
                            $attachment->filepath,
                            $attachment->filename,
                            'base64',
                            $attachment->contentType
                        );
                    }
                }
            }

            // Send email
            if (!$this->mailer->send()) {
                throw new Exception('Mailer Error: ' . $this->mailer->ErrorInfo);
            }
        } finally {
            // Clean up SMTP connection
            $this->mailer->smtpClose();
        }
    }
}
