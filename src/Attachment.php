<?php

namespace Shoutbox;

class Attachment {
    public ?string $filename = null;
    public string $filepath;
    public ?string $contentType = null;
    public $content = null; // string or Buffer
}
