<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class BlogPost extends Model implements Sortable
{
    use HasTranslations, SortableTrait;

    public array $translatable = [
        'title',
        'slug',
        'excerpt',
        'author',
        'content',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'author',
        'content',
        'image',
        'order_column',
        'published_at',
        'is_published',
        'views_count',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
        'views_count' => 'integer',
        'order_column' => 'integer',
    ];

    public array $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
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

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order_column');
    }
}
