<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CustomerLogo extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'name',
        'light_logo',
        'dark_logo',
        'website_url',
        'order_column',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_column' => 'integer',
    ];

    public array $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    public function darkLogoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'dark_logo');
    }

    public function lightLogoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'light_logo');
    }

    public function getDarkLogoUrlAttribute(): ?string
    {
        if (! $this->dark_logo) {
            return null;
        }

        if (is_numeric($this->dark_logo)) {
            return Media::find($this->dark_logo)?->url;
        }

        return str_starts_with($this->dark_logo, 'http')
            ? $this->dark_logo
            : Storage::disk('public')->url($this->dark_logo);
    }

    public function getLightLogoUrlAttribute(): ?string
    {
        if (! $this->light_logo) {
            return null;
        }

        if (is_numeric($this->light_logo)) {
            return Media::find($this->light_logo)?->url;
        }

        return str_starts_with($this->light_logo, 'http')
            ? $this->light_logo
            : Storage::disk('public')->url($this->light_logo);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order_column');
    }
}
