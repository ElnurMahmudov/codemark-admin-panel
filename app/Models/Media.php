<?php
namespace App\Models;

use Awcodes\Curator\Models\Media as CuratorMedia;
use App\Observers\MediaCreatedObserver;

class Media extends CuratorMedia
{
    protected static function booted()
    {
        static::observe(MediaCreatedObserver::class);
    }
}

