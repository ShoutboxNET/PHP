<?php

require 'vendor/autoload.php';

use Shoutbox\Shoutbox;
use Shoutbox\EmailOptions;

$shoutbox = new Shoutbox();

$options = new EmailOptions();
$options->name = "Vlad";
$options->from = "no-reply@shoutbox.net";
$options->to = "test@example.com";
$options->subject = "A question about the meetup";
$options->html = "<b>Hi, Are you still going to that meetup?</b>";

$shoutbox->sendEmail($options);