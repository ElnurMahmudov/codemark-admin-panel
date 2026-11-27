<?php

namespace App\Filament\Actions;

use App\Services\GeminiTranslationService;
use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TranslateWithAiAction
{
    /**
     * Action for Project form
     */
    public static function forProject(): Action
    {
        return static::baseAction()
            ->action(function (Get $get, Set $set) {
                $azTitle = $get('title.az');
                $azDesc = $get('description.az');
                $azSlug = $get('slug.az') ?: Str::slug($azTitle ?? '');

                if (blank($azTitle) && blank($azDesc)) {
                    static::notifyMissingSource('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) tabında ən azı başlıq və ya qısa təsvir qeyd edin.');

                    return;
                }

                $payload = [
                    'title' => $azTitle,
                    'slug' => $azSlug,
                    'description' => $azDesc,
                    'challenge' => $get('challenge.az'),
                    'solution' => $get('solution.az'),
                    'overview' => $get('overview.az'),
                    'features' => $get('features.az'),
                    'results' => $get('results.az'),
                    'specs' => $get('specs.az'),
                    'meta_title' => $get('meta_title.az') ?: ($azTitle ? "{$azTitle} — Codemark" : null),
                    'meta_description' => $get('meta_description.az') ?: $azDesc,
                ];

                static::executeTranslation(
                    $payload,
                    'Bütün məzmun, göstəricilər və SEO sahələri İngilis və Rus dillərinə tərcümə olundu.',
                    function (string $lang, array $item) use ($set) {
                        if (! empty($item['title']) && is_string($item['title'])) {
                            $set("title.{$lang}", $item['title']);
                        }
                        static::applySlug($set, $lang, $item['slug'] ?? null, $item['title'] ?? null);
                        if (! empty($item['description'])) $set("description.{$lang}", $item['description']);
                        if (! empty($item['challenge'])) $set("challenge.{$lang}", $item['challenge']);
                        if (! empty($item['solution'])) $set("solution.{$lang}", $item['solution']);
                        if (! empty($item['overview'])) $set("overview.{$lang}", $item['overview']);
                        if (! empty($item['features'])) $set("features.{$lang}", $item['features']);
                        if (! empty($item['results'])) $set("results.{$lang}", $item['results']);
                        if (! empty($item['specs'])) $set("specs.{$lang}", $item['specs']);
                        if (! empty($item['meta_title'])) $set("meta_title.{$lang}", $item['meta_title']);
                        if (! empty($item['meta_description'])) $set("meta_description.{$lang}", $item['meta_description']);
                    }
                );
            });
    }

    /**
     * Action for BlogPost form
     */
    public static function forBlogPost(): Action
    {
        return static::baseAction()
            ->action(function (Get $get, Set $set) {
                $azTitle = $get('title.az');
                $azContent = $get('content.az');

                if (blank($azTitle) && blank($azContent)) {
                    static::notifyMissingSource('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) tabında ən azı başlıq və ya məzmun qeyd edin.');

                    return;
                }

                $payload = [
                    'title' => $azTitle,
                    'slug' => $get('slug.az'),
                    'excerpt' => $get('excerpt.az'),
                    'author' => $get('author.az'),
                    'content' => $azContent,
                    'meta_title' => $get('meta_title.az') ?: ($azTitle ? "{$azTitle} — Codemark Bloq" : null),
                    'meta_description' => $get('meta_description.az') ?: $get('excerpt.az'),
                    'meta_keywords' => $get('meta_keywords.az'),
                ];

                static::executeTranslation(
                    $payload,
                    'Məqalənin başlığı, linki, mətni və SEO sahələri İngilis və Rus dillərinə tərcümə olundu.',
                    function (string $lang, array $item) use ($set) {
                        if (! empty($item['title']) && is_string($item['title'])) {
                            $set("title.{$lang}", $item['title']);
                        }
                        static::applySlug($set, $lang, $item['slug'] ?? null, $item['title'] ?? null);
                        if (! empty($item['excerpt'])) $set("excerpt.{$lang}", $item['excerpt']);
                        if (! empty($item['author'])) $set("author.{$lang}", $item['author']);
                        if (! empty($item['content']) && is_string($item['content'])) {
                            $normalizedContent = static::normalizeRichContent($item['content']);
                            if ($normalizedContent !== null) {
                                $set("content.{$lang}", $normalizedContent);
                            }
                        }
                        if (! empty($item['meta_title'])) $set("meta_title.{$lang}", $item['meta_title']);
                        if (! empty($item['meta_description'])) $set("meta_description.{$lang}", $item['meta_description']);
                        if (! empty($item['meta_keywords'])) $set("meta_keywords.{$lang}", $item['meta_keywords']);
                    }
                );
            });
    }

    /**
     * Action for BlogCategory form
     */
    public static function forBlogCategory(): Action
    {
        return static::baseAction()
            ->action(function (Get $get, Set $set) {
                $azName = $get('name.az');

                if (blank($azName)) {
                    static::notifyMissingSource('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) bölməsində ad daxil edin.', 'Tərcümə üçün kateqoriya adı yoxdur');

                    return;
                }

                $payload = [
                    'name' => $azName,
                    'slug' => $get('slug.az'),
                    'description' => $get('description.az'),
                    'meta_title' => $get('meta_title.az'),
                    'meta_description' => $get('meta_description.az'),
                ];

                static::executeTranslation(
                    $payload,
                    'Kateqoriya məlumatları İngilis və Rus dillərinə tərcümə olundu.',
                    function (string $lang, array $item) use ($set) {
                        if (! empty($item['name']) && is_string($item['name'])) {
                            $set("name.{$lang}", $item['name']);
                        }
                        static::applySlug($set, $lang, $item['slug'] ?? null, $item['name'] ?? null);
                        if (! empty($item['description'])) $set("description.{$lang}", $item['description']);
                        if (! empty($item['meta_title'])) $set("meta_title.{$lang}", $item['meta_title']);
                        if (! empty($item['meta_description'])) $set("meta_description.{$lang}", $item['meta_description']);
                    }
                );
            });
    }

    /**
     * Action for ProjectCategory form
     */
    public static function forProjectCategory(): Action
    {
        return static::baseAction()
            ->action(function (Get $get, Set $set) {
                $azName = $get('name.az');

                if (blank($azName)) {
                    static::notifyMissingSource('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) bölməsində ad daxil edin.', 'Tərcümə üçün kateqoriya adı yoxdur');

                    return;
                }

                $payload = [
                    'name' => $azName,
                    'slug' => $get('slug.az'),
                    'description' => $get('description.az'),
                ];

                static::executeTranslation(
                    $payload,
                    'Kateqoriya məlumatları İngilis və Rus dillərinə tərcümə olundu.',
                    function (string $lang, array $item) use ($set) {
                        if (! empty($item['name']) && is_string($item['name'])) {
                            $set("name.{$lang}", $item['name']);
                        }
                        static::applySlug($set, $lang, $item['slug'] ?? null, $item['name'] ?? null);
                        if (! empty($item['description'])) $set("description.{$lang}", $item['description']);
                    }
                );
            });
    }

    /**
     * Action for Tag form
     */
    public static function forTag(): Action
    {
        return static::baseAction()
            ->action(function (Get $get, Set $set) {
                $azName = $get('name.az');

                if (blank($azName)) {
                    static::notifyMissingSource('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) bölməsində teq adını daxil edin.', 'Tərcümə üçün teq adı yoxdur');

                    return;
                }

                $payload = [
                    'name' => $azName,
                    'slug' => $get('slug.az'),
                ];

                static::executeTranslation(
                    $payload,
                    'Teq məlumatları İngilis və Rus dillərinə tərcümə olundu.',
                    function (string $lang, array $item) use ($set) {
                        if (! empty($item['name']) && is_string($item['name'])) {
                            $set("name.{$lang}", $item['name']);
                        }
                        static::applySlug($set, $lang, $item['slug'] ?? null, $item['name'] ?? null);
                    }
                );
            });
    }

    /**
     * Execute translation, apply language results, and send Filament notifications.
     *
     * @param  Closure(string, array<string, mixed>): void  $applyLanguage
     */
    protected static function executeTranslation(array $payload, string $successBody, Closure $applyLanguage): void
    {
        try {
            $translations = app(GeminiTranslationService::class)->translateFields($payload);

            foreach (['en', 'ru'] as $lang) {
                if (! isset($translations[$lang]) || ! is_array($translations[$lang])) {
                    continue;
                }

                $applyLanguage($lang, $translations[$lang]);
            }

            Notification::make()
                ->title('Uğurla tərcümə edildi!')
                ->body($successBody)
                ->success()
                ->send();
        } catch (Throwable $e) {
            Log::error('AI translation action failed: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            Notification::make()
                ->title('Tərcümə zamanı xəta baş verdi')
                ->body(mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8'))
                ->danger()
                ->send();
        }
    }

    /**
     * Set localized slug safely from translated slug or fallback label/title.
     */
    protected static function applySlug(Set $set, string $lang, mixed $slug, mixed $fallback): void
    {
        if (! empty($slug) && is_string($slug)) {
            $set("slug.{$lang}", Str::slug($slug));
        } elseif (! empty($fallback) && is_string($fallback)) {
            $set("slug.{$lang}", Str::slug($fallback));
        }
    }

    /**
     * Ensure HTML content passed to RichEditor/TipTap never causes DOMParser TypeError.
     */
    public static function normalizeRichContent(string $html): ?string
    {
        $clean = trim(mb_convert_encoding($html, 'UTF-8', 'UTF-8'));
        if ($clean === '') {
            return null;
        }

        // TipTap's DOMParser throws TypeError if HTML has only comments/style or empty body text
        $withoutHeadTags = preg_replace('/<(style|script)\b[^>]*>[\s\S]*?<\/\1>/i', '', $clean) ?? $clean;
        $visibleText = trim(strip_tags($withoutHeadTags));
        $hasMediaOrBreak = (bool) preg_match('/<(img|hr|br|iframe|video|svg|table)\b/i', $clean);
        if ($visibleText === '' && ! $hasMediaOrBreak) {
            return null;
        }

        return $clean;
    }

    protected static function notifyMissingSource(string $body, string $title = 'Tərcümə üçün məlumat yoxdur'): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->warning()
            ->send();
    }

    /**
     * Base styled action
     */
    protected static function baseAction(string $name = 'translate_ai'): Action
    {
        return Action::make($name)
            ->label('AI ilə Tərcümə Et')
            ->icon('heroicon-o-sparkles')
            ->color('primary')
            ->tooltip('Azərbaycan dilindəki məlumatları Gemini AI ilə İngilis və Rus dillərinə tərcümə edir')
            ->requiresConfirmation()
            ->modalHeading('AI ilə Avtomatik Tərcümə')
            ->modalDescription('Azərbaycan dilində daxil etdiyiniz məlumatlar əsasında İngilis və Rus dilləri avtomatik tərcümə ediləcək. Davam etmək istəyirsiniz?')
            ->modalSubmitActionLabel('Bəli, Tərcümə Et');
    }
}
