<?php

namespace App\Observers;

use App\Models\BlogPost;
use App\Models\CustomerLogo;
use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentRevalidationObserver
{
    public function saved($model): void
    {
        $this->triggerNextjsRevalidation($model);
    }

    public function deleted($model): void
    {
        $this->triggerNextjsRevalidation($model);
    }

    protected function triggerNextjsRevalidation($model): void
    {
        $secret = config('services.nextjs.revalidation_secret');
        $frontendUrl = config('services.nextjs.url'); // https://codemark.az

        if (! $frontendUrl || ! $secret) {
            return;
        }

        $tag = match (get_class($model)) {
            BlogPost::class => 'blog',
            Project::class => 'projects',
            CustomerLogo::class => 'logos',
            default => 'all',
        };

        try {
            Http::timeout(3)->post("{$frontendUrl}/api/revalidate", [
                'secret' => $secret,
                'tag' => $tag,
            ]);
        } catch (Exception $e) {
            Log::error("Next.js revalidation failed: " . $e->getMessage());
        }
    }
}
