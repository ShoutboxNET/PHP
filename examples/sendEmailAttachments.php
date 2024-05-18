<?php

require 'vendor/autoload.php';

use Shoutbox\Shoutbox;
use Shoutbox\EmailOptions;
use Shoutbox\Attachment;

$shoutbox = new Shoutbox();

$options = new EmailOptions();
$options->name = "Vlad";
$options->from = "no-reply@shoutbox.net";
$options->to = "test@example.com";
$options->subject = "A question about the meetup";
$options->html = "<b>Hi, Are you still going to that meetup?</b>";

$attachment = new Attachment();
$attachment->filepath = "./examples/important.txt";

$options->attachments[] = $attachment;

$shoutbox->sendEmail($options);