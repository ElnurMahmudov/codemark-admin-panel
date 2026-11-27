<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GeminiTranslationService
{
    protected const TOTAL_BUDGET_SECONDS = 300.0;
    protected const MAX_REQUEST_TIMEOUT_SECONDS = 120;
    protected const CONNECT_TIMEOUT_SECONDS = 15;

    protected ?string $apiKey;
    protected array $models;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $primaryModel = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.5-flash-lite');

        // Resilient, ultra-fast, high-quota models across multiple model clusters.
        // Fast flash-lite models go first; gemini-3-flash-preview serves as fallback with minimal thinking.
        $this->models = array_values(array_unique(array_filter([
            $primaryModel,
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-flash-lite-latest',
            'gemini-3-flash-preview',
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
        @set_time_limit(360);

        if (empty($this->apiKey)) {
            throw new Exception('Gemini API açarı (.env daxilində GEMINI_API_KEY) təyin edilməyib.');
        }

        // Filter out completely null or empty values
        $payloadData = array_filter($data, fn ($value) => filled($value));
        if (empty($payloadData)) {
            return [];
        }

        [$sanitizedPayload, $inlineDataMap] = $this->extractInlineDataUris($payloadData);
        $deadline = microtime(true) + self::TOTAL_BUDGET_SECONDS;

        $results = [];
        $pendingLanguages = array_values($targetLanguages);
        $lastErrors = [];

        foreach ($this->models as $model) {
            if (empty($pendingLanguages)) {
                break;
            }

            $apiVersion = (str_contains($model, 'preview') || str_contains($model, 'latest')) ? 'v1beta' : 'v1';
            $endpoint = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$model}:generateContent?key={$this->apiKey}";

            $attempts = 0;
            $maxAttempts = 2;

            while ($attempts < $maxAttempts && ! empty($pendingLanguages)) {
                $remaining = $deadline - microtime(true);
                if ($remaining <= 2.0) {
                    break 2;
                }

                $attempts++;
                $requestTimeout = max(3, min(self::MAX_REQUEST_TIMEOUT_SECONDS, (int) floor($remaining)));

                Log::info("Gemini translating [" . implode(',', $pendingLanguages) . "] via {$model} ({$apiVersion}), attempt {$attempts}");

                $prompts = [];
                foreach ($pendingLanguages as $lang) {
                    $prompts[$lang] = $this->buildPrompt($sanitizedPayload, $lang, $sourceLanguage);
                }

                $generationConfig = [
                    'response_mime_type' => 'application/json',
                    'temperature' => 0.1,
                ];

                if (str_contains($model, 'gemini-3') && ! str_contains($model, 'lite') && $apiVersion === 'v1beta') {
                    $generationConfig['thinkingConfig'] = [
                        'thinkingLevel' => 'minimal',
                    ];
                }

                $responses = Http::pool(function (Pool $pool) use ($prompts, $endpoint, $requestTimeout, $generationConfig) {
                    $requests = [];
                    foreach ($prompts as $lang => $prompt) {
                        $requests[] = $pool->as($lang)
                            ->connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
                            ->timeout($requestTimeout)
                            ->post($endpoint, [
                                'contents' => [
                                    [
                                        'parts' => [
                                            ['text' => $prompt],
                                        ],
                                    ],
                                ],
                                'generationConfig' => $generationConfig,
                            ]);
                    }

                    return $requests;
                });

                $retryable503 = [];

                foreach ($pendingLanguages as $lang) {
                    $response = $responses[$lang] ?? null;

                    if ($response instanceof Throwable) {
                        $lastErrors[$lang] = $this->sanitizeUtf8("Model {$model} exception: " . $response->getMessage());
                        Log::warning("Gemini model {$model} ({$lang}) exception: {$lastErrors[$lang]}");
                        continue;
                    }

                    if (! $response instanceof Response) {
                        $lastErrors[$lang] = "Model {$model}: cavab alınmadı.";
                        continue;
                    }

                    if ($response->successful()) {
                        $decoded = $this->extractTranslatedArray($response, $lang);
                        if (is_array($decoded) && ! empty($decoded)) {
                            $results[$lang] = $this->restoreInlineDataUris($decoded, $inlineDataMap);
                            unset($lastErrors[$lang]);
                            Log::info("Gemini translation to {$lang} succeeded via {$model}");
                            continue;
                        }

                        $lastErrors[$lang] = "Model {$model}: JSON cavabı parse edilə bilmədi.";
                        Log::warning("Gemini model {$model} ({$lang}) returned unparseable JSON.");
                        continue;
                    }

                    $statusCode = $response->status();
                    $errorMsg = $this->sanitizeUtf8((string) ($response->json('error.message') ?? $response->body()));

                    if ($statusCode === 503 && $attempts < $maxAttempts) {
                        $retryable503[] = $lang;
                        Log::warning("Gemini model {$model} ({$lang}) 503 high demand spike, will retry...");
                    } elseif ($statusCode === 429) {
                        $lastErrors[$lang] = "Model {$model} kvota limiti aşılıb (429).";
                        Log::warning("Gemini model {$model} ({$lang}) quota exceeded (429), switching to next model.");
                    } else {
                        $lastErrors[$lang] = "Model {$model} (Status {$statusCode}): " . Str::limit($errorMsg, 200);
                        Log::warning("Gemini model {$model} ({$lang}) error: {$lastErrors[$lang]}");
                    }
                }

                // Keep only languages that have not succeeded yet
                $nextPending = [];
                foreach ($pendingLanguages as $lang) {
                    if (! isset($results[$lang])) {
                        $nextPending[] = $lang;
                    }
                }

                if (! empty($retryable503) && ($deadline - microtime(true)) > 5.0) {
                    // Only retry the languages that hit 503 on this model
                    $pendingLanguages = $retryable503;
                    usleep(1000000);
                } else {
                    $pendingLanguages = $nextPending;
                    break;
                }
            }

            // Recompute all remaining unfinished languages before trying next model
            $pendingLanguages = array_values(array_filter(
                $targetLanguages,
                fn (string $lang) => ! isset($results[$lang])
            ));
        }

        if (! empty($pendingLanguages)) {
            $failedLang = $pendingLanguages[0];
            $langNames = ['en' => 'English', 'ru' => 'Russian', 'az' => 'Azerbaijani'];
            $targetName = $langNames[$failedLang] ?? strtoupper($failedLang);
            $errorDetail = $lastErrors[$failedLang] ?? 'Server vaxt limitinə görə cavab alınmadı.';

            Log::error("Gemini translation failed for [" . implode(',', $pendingLanguages) . "]: " . $errorDetail);
            throw new Exception($this->sanitizeUtf8("Gemini tərcümə xətası ({$targetName}): {$errorDetail}"));
        }

        return $results;
    }

    /**
     * Build prompt for a single target language.
     */
    protected function buildPrompt(array $payloadData, string $targetLanguage, string $sourceLanguage): string
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
4. Do not translate the brand name "Codemark", registered trademarks, or placeholder tokens like __INLINE_DATA_0__.
5. If any field represents a slug (e.g. "slug"), generate a clean lowercase URL slug (hyphen-separated) based on the translated title/name in {$targetName}.
6. Output MUST be a valid JSON object whose keys exactly match the input JSON keys, with values translated into {$targetName}.
PROMPT;

        $encodedPayload = json_encode($payloadData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) ?: '{}';

        return $systemPrompt . "\n\nTranslate this JSON from {$sourceName} to {$targetName}:\n" . $encodedPayload;
    }

    /**
     * Extract and decode the JSON object from a Gemini HTTP response.
     */
    protected function extractTranslatedArray(Response $response, string $targetLanguage): ?array
    {
        $parts = $response->json('candidates.0.content.parts', []);
        $rawText = '';
        foreach ($parts as $part) {
            if (isset($part['text']) && ! ($part['thought'] ?? false)) {
                $rawText .= $part['text'];
            }
        }

        if (empty($rawText)) {
            $rawText = (string) $response->json('candidates.0.content.parts.0.text', '');
        }

        if (! filled($rawText)) {
            return null;
        }

        $decoded = $this->parseJsonResponse($rawText);

        // If model wrapped response inside {"en": {...}}, unwrap it
        if (isset($decoded[$targetLanguage]) && is_array($decoded[$targetLanguage])) {
            $decoded = $decoded[$targetLanguage];
        }

        return is_array($decoded) && ! empty($decoded) ? $decoded : null;
    }

    /**
     * Replace base64 data URIs with lightweight placeholders before sending to LLM.
     *
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    protected function extractInlineDataUris(array $payload): array
    {
        $map = [];
        $counter = 0;

        array_walk_recursive($payload, function (&$value) use (&$map, &$counter) {
            if (! is_string($value) || ! str_contains($value, 'data:')) {
                return;
            }

            $value = preg_replace_callback('/data:[^;"\'\s>]+;base64,[A-Za-z0-9+\/=]+/', function ($matches) use (&$map, &$counter) {
                $token = "__INLINE_DATA_{$counter}__";
                $counter++;
                $map[$token] = $matches[0];

                return $token;
            }, $value);
        });

        return [$payload, $map];
    }

    /**
     * Restore placeholder tokens back to original base64 data URIs.
     */
    protected function restoreInlineDataUris(array $decoded, array $map): array
    {
        if (empty($map)) {
            return $decoded;
        }

        array_walk_recursive($decoded, function (&$value) use ($map) {
            if (is_string($value)) {
                $value = strtr($value, $map);
            }
        });

        return $decoded;
    }

    /**
     * Ensure strings are valid UTF-8 so Livewire JSON serialization never crashes.
     */
    protected function sanitizeUtf8(string $text): string
    {
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    /**
     * Parse and clean JSON string from LLM output.
     */
    protected function parseJsonResponse(string $rawText): ?array
    {
        $clean = trim($this->sanitizeUtf8($rawText));

        // Strip markdown code fences if present (e.g. ```json ... ```)
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $clean, $matches)) {
            $clean = trim($matches[1]);
        }

        $decoded = json_decode($clean, true);

        // Fallback: extract substring between outermost { and }
        if (! is_array($decoded)) {
            $firstBrace = strpos($clean, '{');
            $lastBrace = strrpos($clean, '}');
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $sub = substr($clean, $firstBrace, $lastBrace - $firstBrace + 1);
                $decoded = json_decode($sub, true);
            }
        }

        if (! is_array($decoded)) {
            return null;
        }

        // Normalize if model returned an indexed list: [{"title": "..."}, ...]
        if (isset($decoded[0]) && is_array($decoded[0])) {
            return $decoded[0];
        }

        return $decoded;
    }
}
