<?php

namespace Tests\Unit;

use App\Filament\Actions\TranslateWithAiAction;
use App\Services\GeminiTranslationService;
use Exception;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiTranslationServiceTest extends TestCase
{
    public function test_translates_blog_fields_in_parallel_and_preserves_inline_data_uris(): void
    {
        Config::set('services.gemini.api_key', 'test-key');
        Config::set('services.gemini.model', 'gemini-3.5-flash-lite');

        $base64Img = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        Http::fake(function (Request $request) {
            $bodyText = $request->data()['contents'][0]['parts'][0]['text'] ?? '';

            // Base64 blob must NOT be sent directly to Gemini
            $this->assertStringNotContainsString('iVBORw0KGgoAAAANSUhEUg', $bodyText);
            $this->assertStringContainsString('__INLINE_DATA_0__', $bodyText);

            if (str_contains($bodyText, 'into English (en)')) {
                return Http::response([
                    'candidates' => [[
                        'content' => [
                            'parts' => [[
                                'text' => json_encode([
                                    'title' => 'AI Agents Guide',
                                    'slug' => 'ai-agents-guide',
                                    'content' => '<p>Hello world</p><img src="__INLINE_DATA_0__" />',
                                ]),
                            ]],
                        ],
                    ]],
                ], 200);
            }

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'title' => 'Руководство по ИИ',
                                'slug' => 'rukovodstvo-po-ii',
                                'content' => '<p>Привет мир</p><img src="__INLINE_DATA_0__" />',
                            ]),
                        ]],
                    ],
                ]],
            ], 200);
        });

        $result = app(GeminiTranslationService::class)->translateFields([
            'title' => 'AI Agentlər Bələdçisi',
            'slug' => 'ai-agentler-beledcisi',
            'content' => "<p>Salam dünya</p><img src=\"{$base64Img}\" />",
        ]);

        $this->assertSame('AI Agents Guide', $result['en']['title']);
        $this->assertSame("<p>Hello world</p><img src=\"{$base64Img}\" />", $result['en']['content']);
        $this->assertSame('Руководство по ИИ', $result['ru']['title']);
        $this->assertSame("<p>Привет мир</p><img src=\"{$base64Img}\" />", $result['ru']['content']);

        Http::assertSentCount(2);
    }

    public function test_only_retries_failed_language_on_partial_503_and_falls_back_cleanly(): void
    {
        Config::set('services.gemini.api_key', 'test-key');
        Config::set('services.gemini.model', 'gemini-3.5-flash-lite');

        $enCalls = 0;
        $ruCalls = 0;

        Http::fake(function (Request $request) use (&$enCalls, &$ruCalls) {
            $bodyText = $request->data()['contents'][0]['parts'][0]['text'] ?? '';
            $url = $request->url();

            if (str_contains($bodyText, 'into English (en)')) {
                $enCalls++;

                return Http::response([
                    'candidates' => [[
                        'content' => [
                            'parts' => [[
                                'text' => json_encode(['title' => 'English Title']),
                            ]],
                        ],
                    ]],
                ], 200);
            }

            $ruCalls++;
            if (str_contains($url, 'gemini-3.5-flash-lite')) {
                return Http::response([
                    'error' => ['message' => 'High demand spike'],
                ], 503);
            }

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode(['title' => 'Русский Заголовок']),
                        ]],
                    ],
                ]],
            ], 200);
        });

        $result = app(GeminiTranslationService::class)->translateFields([
            'title' => 'Azərbaycan Başlığı',
        ]);

        $this->assertSame('English Title', $result['en']['title']);
        $this->assertSame('Русский Заголовок', $result['ru']['title']);
        $this->assertSame(1, $enCalls, 'Successful language must not be re-requested on fallback.');
        $this->assertSame(3, $ruCalls, 'Failed language should retry 503 once and succeed on fallback model.');
    }

    public function test_throws_clean_utf8_exception_when_all_models_fail(): void
    {
        Config::set('services.gemini.api_key', 'test-key');
        Config::set('services.gemini.model', 'gemini-3.5-flash-lite');

        Http::fake([
            '*' => Http::response(['error' => ['message' => 'Quota exceeded']], 429),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Gemini tərcümə xətası');

        app(GeminiTranslationService::class)->translateFields([
            'title' => 'Test Başlıq',
        ]);
    }

    public function test_normalize_rich_content_prevents_tiptap_dom_parser_crash(): void
    {
        $this->assertNull(TranslateWithAiAction::normalizeRichContent(''));
        $this->assertNull(TranslateWithAiAction::normalizeRichContent("   \n\t  "));
        $this->assertNull(TranslateWithAiAction::normalizeRichContent('<!-- comment only -->'));
        $this->assertNull(TranslateWithAiAction::normalizeRichContent('<style>.x{}</style>'));

        $valid = TranslateWithAiAction::normalizeRichContent('  <p>Translated blog body</p>  ');
        $this->assertSame('<p>Translated blog body</p>', $valid);

        $doc = RichContentRenderer::make()->getEditor()->setContent($valid)->getDocument();
        $this->assertSame('doc', $doc['type']);
    }
}
