<?php

namespace Shoutbox;

class EmailOptions {
    public string $from;
    public ?string $name = null;
    public $to; // string or array of strings
    public string $subject;
    public ?string $html = null;
    public ?string $text = null;
    public ?array $attachments = [];
    public ?string $replyTo = null;
    public ?array $tags = [];
    public ?array $headers = [];
    public $cc; // string or array of strings
}
