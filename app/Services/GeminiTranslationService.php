<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiTranslationService
{
    protected ?string $apiKey;
    protected array $models;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $primaryModel = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.5-flash-lite');

        $this->models = array_unique([
            $primaryModel,
            'gemini-3.5-flash-lite',
            'gemini-3.5-flash',
            'gemini-3.1-flash-lite',
            'gemini-flash-latest',
        ]);
    }

    /**
     * Translates an associative array of fields from Azerbaijani into target languages (EN, RU).
     *
     * @param  array<string, mixed>  $data  Input fields in source language (e.g. ['title' => '...', 'description' => '...'])
     * @param  array<string>  $targetLanguages  Default ['en', 'ru']
     * @param  string  $sourceLanguage  Default 'az'
     * @return array<string, array<string, mixed>>  Keyed by target language code: ['en' => [...], 'ru' => [...]]
     */
    public function translateFields(array $data, array $targetLanguages = ['en', 'ru'], string $sourceLanguage = 'az'): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API açarı (.env daxilində GEMINI_API_KEY) təyin edilməyib.');
        }

        // Filter out completely null or empty values
        $payloadData = array_filter($data, fn ($value) => filled($value));
        if (empty($payloadData)) {
            return [];
        }

        $langsList = implode(', ', $targetLanguages);
        $systemPrompt = <<<PROMPT
You are a professional native translator for "Codemark", an elite software development, web application, and digital branding agency.
Your task is to translate the provided Azerbaijani ({$sourceLanguage}) content into the following target languages: {$langsList}.

CRITICAL RULES:
1. Maintain high-end modern tech, UI/UX, and business agency terminology.
2. Preserve the exact JSON keys, array structures, and hierarchy.
3. For RichText/HTML fields (like article content), preserve all HTML tags (e.g. <p>, <strong>, <em>, <ul>, <li>, <a>, <h1>), translating only the text content inside them.
4. Do not translate the brand name "Codemark" or proper brand trademarks.
5. If any field represents a slug (e.g., "slug"), generate a clean lowercase URL slug (hyphen-separated) based on the translated title or name in that target language.
6. Return a valid JSON object whose top-level keys are EXACTLY the target language codes: {$langsList}.
   Each language object MUST contain the translated version of all input fields matching the input structure.
PROMPT;

        $userPrompt = "Translate the following JSON content:\n" . json_encode($payloadData, JSON_UNESCAPED_UNICODE);

        $lastError = null;

        foreach ($this->models as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::timeout(35)
                        ->post($endpoint, [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => $systemPrompt . "\n\n" . $userPrompt],
                                    ],
                                ],
                            ],
                            'generationConfig' => [
                                'response_mime_type' => 'application/json',
                            ],
                        ]);

                    if ($response->successful()) {
                        $rawText = $response->json('candidates.0.content.parts.0.text');
                        if (filled($rawText)) {
                            $decoded = json_decode($rawText, true);
                            if (is_array($decoded)) {
                                return $decoded;
                            }
                        }
                    }

                    $statusCode = $response->status();
                    $errorMsg = $response->json('error.message') ?? $response->body();
                    $lastError = "Model {$model} (Status {$statusCode}): {$errorMsg}";

                    // If temporary spike, sleep 1.5s before retry
                    if ($statusCode === 503 || $statusCode === 429) {
                        usleep(1500000); // 1.5s
                        continue;
                    }

                    // Otherwise try next model
                    break;
                } catch (Exception $e) {
                    $lastError = "Model {$model} exception: " . $e->getMessage();
                    usleep(1000000);
                }
            }
        }

        Log::error('Gemini all models failed: ' . $lastError);
        throw new Exception('Gemini tərcümə xətası: ' . ($lastError ?: 'Bütün modellərdən cavab alınmadı.'));
    }
}
