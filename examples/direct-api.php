<?php

// Example of using Shoutbox API directly without the client library
$apiKey = getenv('SHOUTBOX_API_KEY') ?: 'your-api-key-here';

$data = [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Test Email via Direct API',
    'html' => '<h1>Hello!</h1><p>This is a test email sent directly via the Shoutbox API.</p>',
    'name' => 'Sender Name',
    'reply_to' => 'reply@example.com'
];

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

if ($httpCode >= 200 && $httpCode < 300) {
    echo "Email sent successfully!\n";
    echo "Response: " . $response . "\n";
} else {
    echo "Failed to send email.\n";
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";
}
