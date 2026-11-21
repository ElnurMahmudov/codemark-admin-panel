<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Awcodes\Curator\Models\Media;

class MainSettings extends Model
{
    protected $fillable = [
        'main_image',
        'logo',
        'favicon',
        'site_title',
        'footer_text',
        'copyright',
        'meta_title',
        'meta_description'
    ];

    public function getMainImageUrlAttribute(): ?string
    {
        if (!$this->main_image) return null;
        return Media::find($this->main_image)?->url;
    }

}
