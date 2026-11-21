<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BlogCategory;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'meta_title',
        'meta_description',
        'blog_category_id',
        'is_published',
        'user_id',
        'deleted_at',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image');
    }

    public function getMainImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        return Media::find($this->image)?->url;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'user_id')->withDefault(function ($user, $post) {
            // Əgər user_id sütunu null-dursa (heç vaxt təyin edilməyibsə)
            if (is_null($post->user_id)) {
                return [
                    'name' => 'Unknown User',
                ];
            }

            // Əgər user_id doludursa, amma AdminUser tapılmırsa (silinibsə)
            return [
                'name' => 'Deleted User',
            ];
        });
    }
}
