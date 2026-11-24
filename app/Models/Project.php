<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements Sortable
{
    use HasTranslations, SortableTrait;

    public array $translatable = [
        'title',
        'slug',
        'description',
        'specs',
        'challenge',
        'solution',
        'overview',
        'features',
        'results',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'project_code',
        'slug',
        'category_id',
        'client',
        'year',
        'bg_light',
        'bg_dark',
        'image',
        'gallery',
        'website_url',
        'title',
        'description',
        'specs',
        'challenge',
        'solution',
        'overview',
        'features',
        'results',
        'order_column',
        'is_featured',
        'is_published',
        'views_count',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'gallery' => 'array',
        'specs' => 'array',
        'overview' => 'array',
        'features' => 'array',
        'results' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'order_column' => 'integer',
        'views_count' => 'integer',
    ];

    public array $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if (empty($project->project_code)) {
                $azSlug = $project->getTranslation('slug', 'az');
                $source = $azSlug ?: ($project->getTranslation('title', 'az') ?: $project->client ?: ('project-' . uniqid()));
                $slug = Str::slug($source);
                $originalSlug = $slug;
                $count = 1;
                while (static::where('project_code', $slug)->where('id', '!=', $project->id ?? 0)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $project->project_code = $slug;
            }

            // Ensure slug.az defaults to project_code or title if empty
            if (empty($project->getTranslation('slug', 'az'))) {
                $azSource = $project->project_code ?: ($project->getTranslation('title', 'az') ?: $project->client);
                if ($azSource) {
                    $project->setTranslation('slug', 'az', Str::slug($azSource));
                }
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function visits(): MorphMany
    {
        return $this->morphMany(SiteVisit::class, 'visitable');
    }

    public function imageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (is_numeric($this->image)) {
            return Media::find($this->image)?->url;
        }

        return str_starts_with($this->image, 'http')
            ? $this->image
            : Storage::disk('public')->url($this->image);
    }

    public function getGalleryUrlsAttribute(): array
    {
        if (empty($this->gallery)) {
            return [];
        }

        $items = is_array($this->gallery) ? $this->gallery : json_decode($this->gallery, true);
        if (! is_array($items)) {
            return [];
        }

        return collect($items)->map(function ($item) {
            if (is_numeric($item)) {
                return Media::find($item)?->url;
            }

            return str_starts_with($item, 'http')
                ? $item
                : Storage::disk('public')->url($item);
        })->filter()->values()->all();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order_column');
    }
}
