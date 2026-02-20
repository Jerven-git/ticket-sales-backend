<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_name',
        'organizer_website',
        'organizer_bio',
        'organizer_facebook_link',
        'organizer_twitter_link',
        'organizer_instagram_link',
        'status'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'imageable');
    }
}
