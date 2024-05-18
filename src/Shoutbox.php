<?php

namespace Shoutbox;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Exception;

class Shoutbox {
    private string $apiKey;
    private const API_ENDPOINT = "https://api.shoutbox.net/send";

    public function __construct(string $apiKey = null) {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();
        } catch (InvalidPathException $e) {
            // No .env file found, continue without it
        }
        $this->apiKey = $apiKey ?? (getenv('SHOUTBOX_API_KEY') ?? '');
        if (!$this->apiKey) {
            throw new Exception("API key is required for Shoutbox");
        }
    }

    private function send(EmailOptions $options): void {
        $extraHeaders = $options->headers ?? [];
        unset($options->headers);

        foreach ($options->attachments as $attachment) {
            if (!$attachment->content) {
                $attachment->content = base64_encode(file_get_contents($attachment->filepath));
            }
            if (!$attachment->filename) {
                $attachment->filename = basename($attachment->filepath);
            }
            if (!$attachment->contentType) {
                $attachment->contentType = mime_content_type($attachment->filepath) ?: "application/octet-stream";
            }
        }

        $emailContent = json_encode($this->filterEmpty((array) $options));
        if (strlen($emailContent) > 1024 * 1024) {
            throw new Exception("Body too large, expect 1MB, " . strlen($emailContent) . " bytes given");
        }

        $headers = array_merge([
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ], $extraHeaders);

        $ch = curl_init(self::API_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $emailContent);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            echo "Email sent successfully: " . $response;
        } else {
            echo "Failed to send email: " . $response;
        }
    }

    private function filterEmpty(array $array): array {
        return array_filter($array, function ($value) {
            if (is_array($value)) {
                return !empty($this->filterEmpty($value));
            }
            return $value !== null && $value !== '';
        });
    }

    public function sendEmail(EmailOptions $options): void {
        $this->send($options);
    }

    public function sendEmails(array $optionsList): void {
        foreach ($optionsList as $options) {
            $this->send($options);
        }
    }
}
