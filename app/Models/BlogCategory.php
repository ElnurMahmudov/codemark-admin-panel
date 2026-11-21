<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            if ($category->children()->exists()) {
                // Əgər alt kateqoriya varsa, silinməni dayandır və xəta qaytar
                throw new \Exception("This category has children. Please delete children first or move them to another category.");
            }
        });
    }
}
