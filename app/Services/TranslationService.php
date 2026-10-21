<?php

namespace Napstar\MultilingualTranslationSystem\Services;

class TranslationService
{
    private string $apiUrl = 'http://127.0.0.1:5000/api/translate';

    public function translate(
        string $text,
        string $sourceLanguage,
        string $targetLanguage
    ): string {

        if ($sourceLanguage === $targetLanguage) {
            return $text;
        }

        $payload = json_encode([
            'source_language' => $sourceLanguage,
            'target_language' => $targetLanguage,
            'text' => $text
        ]);

        if ($payload === false) {
            throw new \Exception(
                'Failed to prepare translation request.'
            );
        }

        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120
        ]);

        $response = curl_exec($ch);

        if ($response === false) {

            $error = curl_error($ch);

            curl_close($ch);

            throw new \Exception(
                'Translation API connection failed: ' . $error
            );
        }

        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

        curl_close($ch);

        $data = json_decode(
            $response,
            true
        );

        if ($httpCode !== 200) {

            $message = $data['error']
                ?? 'Translation API returned an error.';

            throw new \Exception($message);
        }

        if (!isset($data['translated_text'])) {

            throw new \Exception(
                'Translation API returned an invalid response.'
            );
        }

        return $data['translated_text'];
    }

    
    public function translateWithConfidence(string $text, string $sourceLanguage, string $targetLanguage): array {

        if ($sourceLanguage === $targetLanguage) {
            return [
                'translated_text' => $text,
                'confidence' => null,
                'confidence_note' => 'Source and target languages are the same.'
            ];
        }

        $payload = json_encode([
            'source_language' => $sourceLanguage,
            'target_language' => $targetLanguage,
            'text' => $text
        ]);

        if ($payload === false) {
            throw new \Exception(
                'Failed to prepare translation request.'
            );
        }

        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 120
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception(
                'Translation API connection failed: ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode !== 200) {
            $message = $data['error']
                ?? 'Translation API returned an error.';

            throw new \Exception($message);
        }

        if (!isset($data['translated_text'])) {
            throw new \Exception(
                'Translation API returned an invalid response.'
            );
        }

        return [
            'translated_text' => $data['translated_text'],
            'confidence' => $data['confidence'] ?? null,
            'confidence_note' => $data['confidence_note']
                ?? 'Confidence information is unavailable.'
        ];
    }

}