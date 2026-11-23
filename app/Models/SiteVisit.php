<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class SiteVisit extends Model
{
    protected $fillable = [
        'url',
        'ip_hash',
        'visitable_type',
        'visitable_id',
        'device_type',
        'browser',
        'os',
        'referer',
        'country',
    ];

    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ]);
    }

    public function scopeLastMonth(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ]);
    }

    public function scopeLastDays(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days)->startOfDay());
    }

    public function scopeFromGoogle(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('referer', 'like', '%google.%')
              ->orWhere('referer', 'like', '%googlequicksearchbox%');
        });
    }

    public function scopeFromAi(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('referer', 'like', '%chatgpt%')
              ->orWhere('referer', 'like', '%openai%')
              ->orWhere('referer', 'like', '%perplexity%')
              ->orWhere('referer', 'like', '%claude.ai%')
              ->orWhere('referer', 'like', '%gemini.google%')
              ->orWhere('referer', 'like', '%copilot%')
              ->orWhere('referer', 'like', '%deepseek%')
              ->orWhere('referer', 'like', '%poe.com%');
        });
    }

    public function scopeFromOtherSearch(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('referer', 'like', '%yandex%')
              ->orWhere('referer', 'like', '%ya.ru%')
              ->orWhere('referer', 'like', '%bing.com%')
              ->orWhere('referer', 'like', '%duckduckgo%')
              ->orWhere('referer', 'like', '%yahoo%')
              ->orWhere('referer', 'like', '%ecosia%')
              ->orWhere('referer', 'like', '%mail.ru%')
              ->orWhere('referer', 'like', '%baidu%');
        });
    }

    public function scopeFromSocial(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('referer', 'like', '%instagram%')
              ->orWhere('referer', 'like', '%linkedin%')
              ->orWhere('referer', 'like', '%lnkd.in%')
              ->orWhere('referer', 'like', '%facebook%')
              ->orWhere('referer', 'like', '%fb.com%')
              ->orWhere('referer', 'like', '%twitter%')
              ->orWhere('referer', 'like', '%x.com%')
              ->orWhere('referer', 'like', '%t.co%')
              ->orWhere('referer', 'like', '%tiktok%')
              ->orWhere('referer', 'like', '%youtube%')
              ->orWhere('referer', 'like', '%telegram%')
              ->orWhere('referer', 'like', '%t.me%')
              ->orWhere('referer', 'like', '%whatsapp%');
        });
    }

    public function scopeDirect(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('referer')
              ->orWhere('referer', 'direct')
              ->orWhere('referer', '');
        });
    }

    /**
     * Categorizes a raw referer string into structured metadata.
     *
     * @return array{type: string, category: string, label: string, icon: string, color: string}
     */
    public static function categorizeReferer(?string $referer): array
    {
        $ref = strtolower(trim($referer ?? ''));

        if (empty($ref) || $ref === 'direct' || $ref === 'none') {
            return [
                'type' => 'direct',
                'category' => 'Birbaşa Giriş',
                'label' => 'Birbaşa (Direct)',
                'icon' => 'heroicon-m-arrow-right-circle',
                'color' => 'gray',
            ];
        }

        // Google
        if (str_contains($ref, 'google.') || str_contains($ref, 'googlequicksearchbox')) {
            return [
                'type' => 'google',
                'category' => 'Google Axtarış',
                'label' => 'Google',
                'icon' => 'heroicon-m-magnifying-glass',
                'color' => 'info',
            ];
        }

        // AI Systems
        if (
            str_contains($ref, 'chatgpt') || str_contains($ref, 'openai') ||
            str_contains($ref, 'perplexity') ||
            str_contains($ref, 'claude.ai') ||
            str_contains($ref, 'gemini.google') ||
            str_contains($ref, 'copilot') ||
            str_contains($ref, 'deepseek') ||
            str_contains($ref, 'poe.com')
        ) {
            $aiName = 'AI Tövsiyəsi';
            if (str_contains($ref, 'chatgpt') || str_contains($ref, 'openai')) $aiName = 'ChatGPT';
            elseif (str_contains($ref, 'perplexity')) $aiName = 'Perplexity AI';
            elseif (str_contains($ref, 'claude')) $aiName = 'Claude AI';
            elseif (str_contains($ref, 'gemini')) $aiName = 'Google Gemini';
            elseif (str_contains($ref, 'copilot')) $aiName = 'MS Copilot';
            elseif (str_contains($ref, 'deepseek')) $aiName = 'DeepSeek';

            return [
                'type' => 'ai',
                'category' => 'AI Tövsiyəsi',
                'label' => $aiName,
                'icon' => 'heroicon-m-sparkles',
                'color' => 'success',
            ];
        }

        // Other Search Engines
        if (
            str_contains($ref, 'yandex') || str_contains($ref, 'ya.ru') ||
            str_contains($ref, 'bing') ||
            str_contains($ref, 'duckduckgo') ||
            str_contains($ref, 'yahoo') ||
            str_contains($ref, 'ecosia') ||
            str_contains($ref, 'mail.ru') ||
            str_contains($ref, 'baidu')
        ) {
            $engineName = 'Digər Axtarış';
            if (str_contains($ref, 'yandex') || str_contains($ref, 'ya.ru')) $engineName = 'Yandex';
            elseif (str_contains($ref, 'bing')) $engineName = 'Bing';
            elseif (str_contains($ref, 'duckduckgo')) $engineName = 'DuckDuckGo';
            elseif (str_contains($ref, 'yahoo')) $engineName = 'Yahoo';

            return [
                'type' => 'search',
                'category' => 'Axtarış Platforması',
                'label' => $engineName,
                'icon' => 'heroicon-m-globe-alt',
                'color' => 'warning',
            ];
        }

        // Social Networks
        if (
            str_contains($ref, 'instagram') ||
            str_contains($ref, 'linkedin') || str_contains($ref, 'lnkd') ||
            str_contains($ref, 'facebook') || str_contains($ref, 'fb.com') ||
            str_contains($ref, 'twitter') || str_contains($ref, 'x.com') || str_contains($ref, 't.co') ||
            str_contains($ref, 'tiktok') ||
            str_contains($ref, 'youtube') ||
            str_contains($ref, 'telegram') || str_contains($ref, 't.me') ||
            str_contains($ref, 'whatsapp')
        ) {
            $socialName = 'Sosial Şəbəkə';
            if (str_contains($ref, 'instagram')) $socialName = 'Instagram';
            elseif (str_contains($ref, 'linkedin') || str_contains($ref, 'lnkd')) $socialName = 'LinkedIn';
            elseif (str_contains($ref, 'facebook') || str_contains($ref, 'fb')) $socialName = 'Facebook';
            elseif (str_contains($ref, 'twitter') || str_contains($ref, 'x.com') || str_contains($ref, 't.co')) $socialName = 'X (Twitter)';
            elseif (str_contains($ref, 'tiktok')) $socialName = 'TikTok';
            elseif (str_contains($ref, 'youtube')) $socialName = 'YouTube';
            elseif (str_contains($ref, 'telegram') || str_contains($ref, 't.me')) $socialName = 'Telegram';
            elseif (str_contains($ref, 'whatsapp')) $socialName = 'WhatsApp';

            return [
                'type' => 'social',
                'category' => 'Sosial Şəbəkə',
                'label' => $socialName,
                'icon' => 'heroicon-m-share',
                'color' => 'danger',
            ];
        }

        return [
            'type' => 'referral',
            'category' => 'Xarici Keçid',
            'label' => $ref,
            'icon' => 'heroicon-m-arrow-top-right-on-square',
            'color' => 'primary',
        ];
    }

    /**
     * Resolves human-friendly page title and type from a URL path.
     *
     * @return array{name: string, type: string, color: string}
     */
    public static function resolvePageInfo(string $url): array
    {
        $cleanPath = '/' . ltrim(parse_url($url, PHP_URL_PATH) ?? '/', '/');

        // Static standard paths
        if ($cleanPath === '/' || $cleanPath === '') {
            return [
                'name' => 'Ana Səhifə (Homepage)',
                'type' => 'Əsas Səhifə',
                'color' => 'success',
            ];
        }

        if (in_array($cleanPath, ['/projects', '/layiheler', '/layihələr'])) {
            return [
                'name' => 'Layihələr Kataloqu',
                'type' => 'Bölmə',
                'color' => 'warning',
            ];
        }

        if (in_array($cleanPath, ['/blog', '/bloq', '/articles', '/meqaleler', '/məqalələr'])) {
            return [
                'name' => 'Bloq / Məqalələr',
                'type' => 'Bölmə',
                'color' => 'primary',
            ];
        }

        if (in_array($cleanPath, ['/about', '/haqqimizda', '/haqqımızda'])) {
            return [
                'name' => 'Haqqımızda',
                'type' => 'Bölmə',
                'color' => 'info',
            ];
        }

        if (in_array($cleanPath, ['/services', '/xidmetler', '/xidmətlər'])) {
            return [
                'name' => 'Xidmətlər',
                'type' => 'Bölmə',
                'color' => 'info',
            ];
        }

        if (in_array($cleanPath, ['/contact', '/elaqe', '/əlaqə'])) {
            return [
                'name' => 'Əlaqə',
                'type' => 'Bölmə',
                'color' => 'gray',
            ];
        }

        // Blog post match: /blog/{slug} or /bloq/{slug}
        if (preg_match('#^/(?:blog|bloq)/([^/]+)#', $cleanPath, $matches)) {
            $slug = $matches[1];
            $post = BlogPost::whereJsonContains('slug->az', $slug)
                ->orWhereJsonContains('slug->en', $slug)
                ->orWhereJsonContains('slug->ru', $slug)
                ->first();

            $title = $post ? ($post->getTranslation('title', 'az') ?? (is_array($post->title) ? reset($post->title) : $post->title)) : $slug;

            return [
                'name' => $title,
                'type' => 'Bloq Məqaləsi',
                'color' => 'primary',
            ];
        }

        // Project match: /projects/{code} or /layiheler/{code}
        if (preg_match('#^/(?:projects|layiheler|layihələr)/([^/]+)#', $cleanPath, $matches)) {
            $code = $matches[1];
            $project = Project::where('project_code', $code)->first();

            $title = $project ? ($project->getTranslation('title', 'az') ?? (is_array($project->title) ? reset($project->title) : $project->title)) : $code;

            return [
                'name' => $title,
                'type' => 'Layihə Səhifəsi',
                'color' => 'warning',
            ];
        }

        return [
            'name' => $cleanPath,
            'type' => 'Daxili Səhifə',
            'color' => 'gray',
        ];
    }

    /**
     * Records a site visit from an HTTP request.
     */
    public static function recordVisit(Request $request, ?Model $visitable = null): self
    {
        $userAgent = $request->userAgent() ?? '';
        $url = $request->input('url') ?: ($request->path() ? '/' . ltrim($request->path(), '/') : '/');
        
        // Sanitize URL to path only
        $parsedUrl = parse_url($url);
        $urlPath = $parsedUrl['path'] ?? '/';

        // Parse referer
        $rawReferer = $request->input('referer') ?: $request->header('referer');
        $refererHost = 'direct';
        if ($rawReferer) {
            $refHost = parse_url($rawReferer, PHP_URL_HOST);
            if ($refHost && ! str_contains($refHost, 'codemark.az') && ! str_contains($refHost, '127.0.0.1') && ! str_contains($refHost, 'localhost')) {
                $refererHost = preg_replace('/^www\./i', '', strtolower($refHost));
            }
        }

        // Detect device
        $device = 'desktop';
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
            $device = 'tablet';
        } elseif (preg_match('/(mobile|ipod|iphone|android|blackberry|iemobile)/i', $userAgent)) {
            $device = 'mobile';
        }

        // Detect browser
        $browser = 'Other';
        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect OS
        $os = 'Other';
        if (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        }

        $ipHash = hash('sha256', ($request->ip() ?? '127.0.0.1') . config('app.key'));

        return static::create([
            'url' => $urlPath,
            'ip_hash' => $ipHash,
            'visitable_type' => $visitable ? get_class($visitable) : null,
            'visitable_id' => $visitable?->getKey(),
            'device_type' => $device,
            'browser' => $browser,
            'os' => $os,
            'referer' => $refererHost,
            'country' => $request->header('CF-IPCountry') ?: null,
        ]);
    }
}
