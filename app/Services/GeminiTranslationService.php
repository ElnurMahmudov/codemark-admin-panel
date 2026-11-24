<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiTranslationService
{
    protected ?string $apiKey;
    protected array $models;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $primaryModel = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.5-flash-lite');

        // Resilient, ultra-fast, high-quota models across multiple model clusters.
        // gemini-3.5-flash-lite is primary, gemini-3-flash-preview provides instant fallback when lite cluster experiences 503 spikes.
        $this->models = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-3.5-flash-lite',
            'gemini-3-flash-preview',
            'gemini-3.1-flash-lite',
            'gemini-flash-lite-latest',
        ])));
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
        @set_time_limit(180);

        if (empty($this->apiKey)) {
            throw new Exception('Gemini API açarı (.env daxilində GEMINI_API_KEY) təyin edilməyib.');
        }

        // Filter out completely null or empty values
        $payloadData = array_filter($data, fn ($value) => filled($value));
        if (empty($payloadData)) {
            return [];
        }

        $results = [];

        // Translating each target language separately ensures lightweight prompts,
        // fast responses (~2-3 sec each), and zero grammar-generation bottlenecks.
        foreach ($targetLanguages as $lang) {
            $results[$lang] = $this->translateSingleLanguage($payloadData, $lang, $sourceLanguage);
        }

        return $results;
    }

    /**
     * Translates payload into a single target language.
     */
    protected function translateSingleLanguage(array $payloadData, string $targetLanguage, string $sourceLanguage = 'az'): array
    {
        $langNames = [
            'en' => 'English',
            'ru' => 'Russian',
            'az' => 'Azerbaijani',
        ];
        $targetName = $langNames[$targetLanguage] ?? strtoupper($targetLanguage);
        $sourceName = $langNames[$sourceLanguage] ?? strtoupper($sourceLanguage);

        $systemPrompt = <<<PROMPT
You are a professional native translator for "Codemark", an elite software development and digital agency.
Translate the provided {$sourceName} ({$sourceLanguage}) JSON content into {$targetName} ({$targetLanguage}).

CRITICAL RULES:
1. Maintain high-end modern tech, UI/UX, and business terminology.
2. Preserve the exact JSON keys, hierarchy, and data structure.
3. For RichText/HTML fields, preserve all HTML tags (e.g. <p>, <strong>, <em>, <ul>, <li>, <a>, <h1>, <h2>, <h3>, <blockquote>, <pre>, <code>), translating only the human-readable text inside them.
4. Do not translate the brand name "Codemark" or registered trademarks.
5. If any field represents a slug (e.g. "slug"), generate a clean lowercase URL slug (hyphen-separated) based on the translated title/name in {$targetName}.
6. Output MUST be a valid JSON object whose keys exactly match the input JSON keys, with values translated into {$targetName}.
PROMPT;

        $userPrompt = "Translate this JSON from {$sourceName} to {$targetName}:\n" . json_encode($payloadData, JSON_UNESCAPED_UNICODE);

        $lastError = null;

        foreach ($this->models as $model) {
            $apiVersion = (str_contains($model, 'preview') || str_contains($model, 'latest')) ? 'v1beta' : 'v1';
            $endpoint = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$model}:generateContent?key={$this->apiKey}";

            $attempts = 0;
            $maxAttempts = 2; // Allow 1 quick retry for transient 503 (high demand spike)

            while ($attempts < $maxAttempts) {
                $attempts++;
                try {
                    Log::info("Gemini translating to {$targetLanguage} via {$model} ({$apiVersion}), attempt {$attempts}");

                    $response = Http::connectTimeout(10)
                        ->timeout(45)
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
                                'temperature' => 0.1,
                            ],
                        ]);

                    if ($response->successful()) {
                        $parts = $response->json('candidates.0.content.parts', []);
                        $rawText = '';
                        foreach ($parts as $part) {
                            if (isset($part['text']) && !($part['thought'] ?? false)) {
                                $rawText .= $part['text'];
                            }
                        }

                        if (empty($rawText)) {
                            $rawText = (string) $response->json('candidates.0.content.parts.0.text', '');
                        }

                        if (filled($rawText)) {
                            $decoded = $this->parseJsonResponse($rawText);

                            // If model wrapped response inside {"en": {...}} or {"data": {...}}, unwrap it
                            if (isset($decoded[$targetLanguage]) && is_array($decoded[$targetLanguage])) {
                                $decoded = $decoded[$targetLanguage];
                            }

                            if (is_array($decoded) && !empty($decoded)) {
                                Log::info("Gemini translation to {$targetLanguage} succeeded via {$model}");
                                return $decoded;
                            }
                        }
                    }

                    $statusCode = $response->status();
                    $errorMsg = $response->json('error.message') ?? $response->body();

                    // If 503 (High Demand spike), wait 1.5s and retry once
                    if ($statusCode === 503 && $attempts < $maxAttempts) {
                        Log::warning("Gemini model {$model} 503 high demand spike, retrying after 1.5s...");
                        usleep(1500000);
                        continue;
                    }

                    if ($statusCode === 429) {
                        Log::warning("Gemini model {$model} quota exceeded (429), switching to next model.");
                        $lastError = "Model {$model} kvota limiti aşılıb (429).";
                        break;
                    }

                    $lastError = "Model {$model} (Status {$statusCode}): " . Str::limit($errorMsg, 200);
                    Log::warning("Gemini model {$model} error: {$lastError}");
                    break;
                } catch (Exception $e) {
                    $lastError = "Model {$model} exception: " . $e->getMessage();
                    Log::warning("Gemini model {$model} exception: {$lastError}");
                    break;
                }
            }
        }

        Log::error("Gemini translation failed for {$targetLanguage}: " . $lastError);
        throw new Exception("Gemini tərcümə xətası ({$targetName}): " . ($lastError ?: 'Cavab alınmadı.'));
    }

    /**
     * Parse and clean JSON string from LLM output.
     */
    protected function parseJsonResponse(string $rawText): ?array
    {
        $clean = trim($rawText);

        // Strip markdown code fences if present (e.g. ```json ... ```)
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $clean, $matches)) {
            $clean = trim($matches[1]);
        }

        $decoded = json_decode($clean, true);

        // Fallback: extract substring between outermost { and }
        if (!is_array($decoded)) {
            $firstBrace = strpos($clean, '{');
            $lastBrace = strrpos($clean, '}');
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $sub = substr($clean, $firstBrace, $lastBrace - $firstBrace + 1);
                $decoded = json_decode($sub, true);
            }
        }

        if (!is_array($decoded)) {
            return null;
        }

        // Normalize if model returned an indexed list: [{"title": "..."}, ...]
        if (isset($decoded[0]) && is_array($decoded[0])) {
            return $decoded[0];
        }

        return $decoded;
    }
}
