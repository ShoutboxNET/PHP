![Header](https://github.com/user-attachments/assets/52d6ff91-3425-4e31-bff3-426bbb6eb113)

<p align="center">
  <a href="https://docs.shoutbox.net/quickstart" style="font-size: 2em; text-decoration: underline; color: #0366d6;">Quickstart Docs</a>
</p>

<p align="center" style="font-size: 1.5em;">
  <b>Language & Framework guides</b>
</p>

<p align="center">
  <a href="https://docs.shoutbox.net/examples/nextjs-lib">Next.js</a> -
  <a href="https://docs.shoutbox.net/examples/typescript">Typescript</a> -
  <a href="https://docs.shoutbox.net/examples/javascript-lib">Javascript</a> -
  <a href="https://docs.shoutbox.net/examples/python-lib">Python</a> -
  <a href="https://docs.shoutbox.net/examples/php-lib">PHP</a> -
  <a href="https://docs.shoutbox.net/examples/php-laravel-lib">Laravel</a> -
  <a href="https://docs.shoutbox.net/examples/go">Go</a>
</p>

# Shoutbox.net Developer API

Shoutbox.net is a Developer API designed to send transactional emails at scale. This documentation covers all integration methods, from direct API calls to full framework integration.

## Setup

For these integrations to work, you will need an <a href="https://hub.shoutbox.net" target="_blank">account</a> on <a href="https://shoutbox.net" target="_blank">Shoutbox.net</a>. You can create and copy the required API key on the <a href="https://hub.shoutbox.net/app/dashboard" target="_blank">Shoutbox.net dashboard</a>!

The API key is required for any call to the <a href="https://shoutbox.net" target="_blank">Shoutbox.net</a> backend; for SMTP, the API key is your password and 'shoutbox' the user to send emails.

## Integration Methods

There are three main ways to integrate with Shoutbox:

1. Direct API calls (no dependencies)
2. Using our PHP library with Composer
3. Laravel framework integration

## 1. Direct API Integration (No Dependencies)

If you want to avoid dependencies, you can make direct API calls:

```php
<?php

// Your API key from Shoutbox.net
$apiKey = 'your-api-key-here';

// Prepare email data
$data = [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Test Email',
    'html' => '<h1>Hello!</h1><p>This is a test email.</p>',
    'name' => 'Sender Name',
    'reply_to' => 'reply@example.com'
];

// Make the API call
$ch = curl_init('https://api.shoutbox.net/send');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle the response
if ($httpCode >= 200 && $httpCode < 300) {
    echo "Email sent successfully!\n";
} else {
    echo "Failed to send email. Status code: $httpCode\n";
}
```

### Direct API Features

- No dependencies required
- Simple cURL implementation
- Full control over the request
- Lightweight integration
- Suitable for simple implementations

## 2. PHP Library with Composer

### Installation

```bash
composer require shoutboxnet/shoutbox
```

### 2.1 API Client Usage

The API client provides an object-oriented interface to the REST API:

```php
<?php

require 'vendor/autoload.php';

use Shoutbox\Client;
use Shoutbox\EmailOptions;
use Shoutbox\Attachment;

// Initialize client
$apiKey = getenv('SHOUTBOX_API_KEY') ?: 'your-api-key-here';
$client = new Client($apiKey);

try {
    // Basic email
    $options = new EmailOptions();
    $options->from = 'sender@example.com';
    $options->to = 'recipient@example.com';
    $options->subject = 'Test Email';
    $options->html = '<h1>Hello!</h1><p>This is a test email.</p>';
    $options->name = 'Sender Name';
    $options->replyTo = 'reply@example.com';

    $client->sendEmail($options);

    // Email with attachment
    $attachment = new Attachment();
    $attachment->filepath = './document.pdf';
    $attachment->filename = 'document.pdf';
    $attachment->contentType = 'application/pdf';

    $optionsWithAttachment = new EmailOptions();
    $optionsWithAttachment->from = 'sender@example.com';
    $optionsWithAttachment->to = 'recipient@example.com';
    $optionsWithAttachment->subject = 'Test Email with Attachment';
    $optionsWithAttachment->html = '<h1>Hello!</h1><p>This email includes an attachment.</p>';
    $optionsWithAttachment->attachments = [$attachment];

    $client->sendEmail($optionsWithAttachment);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### 2.2 SMTP Client Usage

The SMTP client provides an alternative way to send emails:

```php
<?php

require 'vendor/autoload.php';

use Shoutbox\SMTPClient;
use Shoutbox\EmailOptions;

$client = new SMTPClient('your-api-key-here');

try {
    // Multiple recipients
    $options = new EmailOptions();
    $options->from = 'sender@example.com';
    $options->to = ['recipient1@example.com', 'recipient2@example.com'];
    $options->subject = 'Test Email';
    $options->html = '<h1>Hello!</h1><p>This is a test email.</p>';
    $options->headers = [
        'X-Custom-Header' => 'Custom Value',
        'X-Priority' => '1'
    ];

    $client->sendEmail($options);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Library Features

- Type-safe email options
- Built-in error handling
- File attachment support
- Custom headers support
- Multiple recipient types (to, cc, bcc)
- Choice between API and SMTP clients

## 3. Laravel Integration

### Installation

1. Install the package:

```bash
composer require shoutboxnet/shoutbox
```

2. Add configuration to `config/services.php`:

```php
'shoutbox' => [
    'key' => env('SHOUTBOX_API_KEY'),
],
```

3. Add to `.env`:

```
SHOUTBOX_API_KEY=your-api-key-here
```

### Usage Examples

#### Basic Usage

```php
use Illuminate\Support\Facades\Mail;

Mail::to('recipient@example.com')
    ->send(new ShoutboxMail('<h1>Hello</h1><p>This is a test email.</p>'));
```

#### Advanced Usage

```php
Mail::to('recipient@example.com')
    ->from('sender@example.com', 'Sender Name')
    ->replyTo('reply@example.com')
    ->cc(['cc1@example.com', 'cc2@example.com'])
    ->send(new ShoutboxMail('<h1>Hello</h1><p>This is a test email.</p>'));
```

#### Queue Support

```php
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Mail::to('recipient@example.com')
            ->send(new ShoutboxMail('<h1>Hello</h1><p>This is a queued email.</p>'));
    }
}
```

### Laravel Features

- Full Laravel Mail integration
- Queue support
- Rate limiting
- Exception handling
- Configuration management
- Service provider auto-discovery

## EmailOptions Reference

The `EmailOptions` class supports:

- **from** (string): Sender's email address
- **to** (string|string[]): Recipient email address(es)
- **subject** (string): Email subject
- **html** (string): HTML content
- **text** (string): Plain text content
- **name** (string): Sender's name
- **replyTo** (string): Reply-to address
- **cc** (string|string[]): CC recipients
- **bcc** (string|string[]): BCC recipients
- **attachments** (Attachment[]): File attachments
- **headers** (array): Custom headers
- **tags** (array): Email tags

### Attachment Properties

- **filepath**: Path to the file
- **filename**: Name for the attachment (optional)
- **contentType**: MIME type (optional)
- **content**: Base64 encoded content (optional)

## Considerations

### API vs SMTP Client

- API Client is recommended for most use cases
- SMTP Client is useful for legacy system compatibility
- Both support the same features

### Security

- Store API keys securely
- Use environment variables
- Validate email addresses
- Sanitize HTML content

### Performance

- Use queues for bulk sending
- Implement rate limiting
- Handle errors gracefully
- Monitor API responses

### Testing

- Use test API keys
- Mock API calls in tests
- Verify email delivery
- Check spam scores

## Development

1. Clone the repository:

```bash
git clone https://github.com/shoutboxnet/shoutbox-PHP.git
```

2. Install dependencies:

```bash
composer install
```

3. Run tests:

```bash
composer test
```

## Support

- GitHub Issues for bug reports
- Email support for critical issues
- Documentation for guides and examples
- Regular updates and maintenance

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
