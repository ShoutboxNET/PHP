<?php

namespace Shoutbox\Laravel;

use Illuminate\Mail\Transport\Transport;
use Shoutbox\Client as ShoutboxClient;
use Shoutbox\EmailOptions;
use Swift_Mime_SimpleMessage;

class ShoutboxTransport extends Transport
{
    protected ShoutboxClient $client;

    public function __construct(string $apiKey)
    {
        $this->client = new ShoutboxClient($apiKey);
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param string[] $failedRecipients
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $options = new EmailOptions();

        // Handle From
        /** @var array<string,string> $from */
        $from = $message->getFrom();
        $fromEmail = (string)array_key_first($from);
        $fromName = (string)($from[$fromEmail] ?? '');

        $options->from = $fromEmail;
        $options->name = $fromName;
        
        // Handle To
        /** @var array<string,string>|null $to */
        $to = $message->getTo();
        $toAddresses = $to ? array_keys($to) : [];
        $options->setTo($toAddresses);
        
        $options->subject = $message->getSubject();
        $options->html = $message->getBody();

        // Handle Reply-To
        /** @var array<string,string>|null $replyTo */
        $replyTo = $message->getReplyTo();
        if ($replyTo) {
            $options->replyTo = (string)array_key_first($replyTo);
        }

        // Handle CC
        /** @var array<string,string>|null $cc */
        $cc = $message->getCc();
        if ($cc) {
            $options->setCc(array_keys($cc));
        }

        // Handle attachments
        $children = $message->getChildren();
        if (!empty($children)) {
            $options->attachments = [];
            foreach ($children as $child) {
                if ($child instanceof \Swift_Attachment) {
                    $attachment = new \Shoutbox\Attachment();
                    $attachment->content = base64_encode($child->getBody());
                    $attachment->filename = $child->getFilename();
                    $attachment->contentType = $child->getContentType();
                    $options->attachments[] = $attachment;
                }
            }
        }

        $this->client->sendEmail($options);

        return $this->numberOfRecipients($message);
    }
}
