<?php

namespace Shoutbox;

class EmailOptions {
    public string $from;
    public ?string $name = null;
    /**
     * @var string|string[] Recipient email address(es)
     */
    public $to;
    public string $subject;
    public ?string $html = null;
    public ?string $text = null;
    /**
     * @var Attachment[]|null
     */
    public ?array $attachments = [];
    public ?string $replyTo = null;
    /**
     * @var string[]|null
     */
    public ?array $tags = [];
    /**
     * @var array<string,string>|null
     */
    public ?array $headers = [];
    /**
     * @var string|string[]|null
     */
    public $cc = null;

    /**
     * Ensures recipients are always in array format
     * @param string|string[] $to
     */
    public function setTo($to): void
    {
        $this->to = is_array($to) ? $to : [$to];
    }

    /**
     * Ensures CC recipients are always in array format
     * @param string|string[]|null $cc
     */
    public function setCc($cc): void
    {
        if ($cc === null) {
            $this->cc = null;
        } else {
            $this->cc = is_array($cc) ? $cc : [$cc];
        }
    }
}
