<?php

namespace Shoutbox;

use Exception;
use RuntimeException;

class Client
{
    private string $apiKey;
    private string $baseURL;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->baseURL = 'https://api.shoutbox.net';
    }

    /**
     * @throws Exception
     */
    public function sendEmail(EmailOptions $options): void
    {
        $data = $this->prepareEmailData($options);
        $jsonData = json_encode($data);

        if (strlen($jsonData) > 1024 * 1024) {
            throw new Exception("Body too large, expect 1MB, " . strlen($jsonData) . " bytes given");
        }

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        if (!empty($options->headers)) {
            foreach ($options->headers as $key => $value) {
                $headers[] = "$key: $value";
            }
        }

        $ch = curl_init($this->baseURL . '/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("Failed to send request: " . $error);
        }

        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error']) ? $errorData['error'] : "HTTP Error: $httpCode";
            throw new RuntimeException("API error: " . $errorMessage);
        }
    }

    private function prepareEmailData(EmailOptions $options): array
    {
        $data = [
            'from' => $options->from,
            'to' => $options->to,
            'subject' => $options->subject,
            'html' => $options->html,
        ];

        if (!empty($options->name)) {
            $data['name'] = $options->name;
        }

        if (!empty($options->replyTo)) {
            $data['reply_to'] = $options->replyTo;
        }

        if (!empty($options->attachments)) {
            $data['attachments'] = array_map(function ($attachment) {
                if (!$attachment->content) {
                    $attachment->content = base64_encode(file_get_contents($attachment->filepath));
                }
                if (!$attachment->filename) {
                    $attachment->filename = basename($attachment->filepath);
                }
                if (!$attachment->contentType) {
                    $attachment->contentType = mime_content_type($attachment->filepath) ?: "application/octet-stream";
                }
                return [
                    'filename' => $attachment->filename,
                    'content' => $attachment->content,
                    'content_type' => $attachment->contentType
                ];
            }, $options->attachments);
        }

        return array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
