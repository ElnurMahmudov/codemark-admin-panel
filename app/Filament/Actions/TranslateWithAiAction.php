<?php

namespace App\Filament\Actions;

use App\Services\GeminiTranslationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    Notification::make()
                        ->title('Tərcümə üçün məlumat yoxdur')
                        ->body('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) tabında ən azı başlıq və ya qısa təsvir qeyd edin.')
                        ->warning()
                        ->send();
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

                try {
                    $translations = app(GeminiTranslationService::class)->translateFields($payload);

                    foreach (['en', 'ru'] as $lang) {
                        if (! isset($translations[$lang]) || ! is_array($translations[$lang])) {
                            continue;
                        }

                        $item = $translations[$lang];

                        if (! empty($item['title'])) {
                            $set("title.{$lang}", $item['title']);
                        }
                        if (! empty($item['slug'])) {
                            $set("slug.{$lang}", Str::slug($item['slug']));
                        } elseif (! empty($item['title'])) {
                            $set("slug.{$lang}", Str::slug($item['title']));
                        }
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

                    Notification::make()
                        ->title('Uğurla tərcümə edildi!')
                        ->body('Bütün məzmun, göstəricilər və SEO sahələri İngilis və Rus dillərinə tərcümə olundu.')
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title('Tərcümə zamanı xəta baş verdi')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
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
                    Notification::make()
                        ->title('Tərcümə üçün məlumat yoxdur')
                        ->body('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) tabında ən azı başlıq və ya məzmun qeyd edin.')
                        ->warning()
                        ->send();
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

                try {
                    $translations = app(GeminiTranslationService::class)->translateFields($payload);

                    foreach (['en', 'ru'] as $lang) {
                        if (! isset($translations[$lang]) || ! is_array($translations[$lang])) {
                            continue;
                        }

                        $item = $translations[$lang];

                        if (! empty($item['title'])) {
                            $set("title.{$lang}", $item['title']);
                        }
                        if (! empty($item['slug'])) {
                            $set("slug.{$lang}", Str::slug($item['slug']));
                        } elseif (! empty($item['title'])) {
                            $set("slug.{$lang}", Str::slug($item['title']));
                        }
                        if (! empty($item['excerpt'])) $set("excerpt.{$lang}", $item['excerpt']);
                        if (! empty($item['author'])) $set("author.{$lang}", $item['author']);
                        if (! empty($item['content'])) $set("content.{$lang}", $item['content']);
                        if (! empty($item['meta_title'])) $set("meta_title.{$lang}", $item['meta_title']);
                        if (! empty($item['meta_description'])) $set("meta_description.{$lang}", $item['meta_description']);
                        if (! empty($item['meta_keywords'])) $set("meta_keywords.{$lang}", $item['meta_keywords']);
                    }

                    Notification::make()
                        ->title('Uğurla tərcümə edildi!')
                        ->body('Məqalənin başlığı, linki, mətni və SEO sahələri İngilis və Rus dillərinə tərcümə olundu.')
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title('Tərcümə zamanı xəta baş verdi')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
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
                    Notification::make()
                        ->title('Tərcümə üçün kateqoriya adı yoxdur')
                        ->body('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) bölməsində ad daxil edin.')
                        ->warning()
                        ->send();
                    return;
                }

                $payload = [
                    'name' => $azName,
                    'slug' => $get('slug.az'),
                    'description' => $get('description.az'),
                    'meta_title' => $get('meta_title.az'),
                    'meta_description' => $get('meta_description.az'),
                ];

                try {
                    $translations = app(GeminiTranslationService::class)->translateFields($payload);

                    foreach (['en', 'ru'] as $lang) {
                        if (! isset($translations[$lang]) || ! is_array($translations[$lang])) {
                            continue;
                        }

                        $item = $translations[$lang];

                        if (! empty($item['name'])) {
                            $set("name.{$lang}", $item['name']);
                        }
                        if (! empty($item['slug'])) {
                            $set("slug.{$lang}", Str::slug($item['slug']));
                        } elseif (! empty($item['name'])) {
                            $set("slug.{$lang}", Str::slug($item['name']));
                        }
                        if (! empty($item['description'])) $set("description.{$lang}", $item['description']);
                        if (! empty($item['meta_title'])) $set("meta_title.{$lang}", $item['meta_title']);
                        if (! empty($item['meta_description'])) $set("meta_description.{$lang}", $item['meta_description']);
                    }

                    Notification::make()
                        ->title('Uğurla tərcümə edildi!')
                        ->body('Kateqoriya məlumatları İngilis və Rus dillərinə tərcümə olundu.')
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title('Tərcümə zamanı xəta baş verdi')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
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
                    Notification::make()
                        ->title('Tərcümə üçün kateqoriya adı yoxdur')
                        ->body('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) bölməsində ad daxil edin.')
                        ->warning()
                        ->send();
                    return;
                }

                $payload = [
                    'name' => $azName,
                    'slug' => $get('slug.az'),
                    'description' => $get('description.az'),
                ];

                try {
                    $translations = app(GeminiTranslationService::class)->translateFields($payload);

                    foreach (['en', 'ru'] as $lang) {
                        if (! isset($translations[$lang]) || ! is_array($translations[$lang])) {
                            continue;
                        }

                        $item = $translations[$lang];

                        if (! empty($item['name'])) {
                            $set("name.{$lang}", $item['name']);
                        }
                        if (! empty($item['slug'])) {
                            $set("slug.{$lang}", Str::slug($item['slug']));
                        } elseif (! empty($item['name'])) {
                            $set("slug.{$lang}", Str::slug($item['name']));
                        }
                        if (! empty($item['description'])) $set("description.{$lang}", $item['description']);
                    }

                    Notification::make()
                        ->title('Uğurla tərcümə edildi!')
                        ->body('Kateqoriya məlumatları İngilis və Rus dillərinə tərcümə olundu.')
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title('Tərcümə zamanı xəta baş verdi')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
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
                    Notification::make()
                        ->title('Tərcümə üçün teq adı yoxdur')
                        ->body('Zəhmət olmasa, əvvəlcə Azərbaycan (AZ) bölməsində teq adını daxil edin.')
                        ->warning()
                        ->send();
                    return;
                }

                $payload = [
                    'name' => $azName,
                    'slug' => $get('slug.az'),
                ];

                try {
                    $translations = app(GeminiTranslationService::class)->translateFields($payload);

                    foreach (['en', 'ru'] as $lang) {
                        if (! isset($translations[$lang]) || ! is_array($translations[$lang])) {
                            continue;
                        }

                        $item = $translations[$lang];

                        if (! empty($item['name'])) {
                            $set("name.{$lang}", $item['name']);
                        }
                        if (! empty($item['slug'])) {
                            $set("slug.{$lang}", Str::slug($item['slug']));
                        } elseif (! empty($item['name'])) {
                            $set("slug.{$lang}", Str::slug($item['name']));
                        }
                    }

                    Notification::make()
                        ->title('Uğurla tərcümə edildi!')
                        ->body('Teq məlumatları İngilis və Rus dillərinə tərcümə olundu.')
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->title('Tərcümə zamanı xəta baş verdi')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
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
