<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
    protected $table = 'contact_information';

    protected $fillable = [
        'phones',
        'emails',
        'address',
        'address_maps',
        'map_url',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
    ];
}
