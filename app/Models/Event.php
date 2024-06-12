<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_title',
        'event_type',
        'event_location',
        'event_link',
        'event_note',
        'event_description',
        'event_refund',
        'event_category',
        'event_sub_category',
        'event_code',
        'event_organizer',
        'start_date',
        'start_time',
        'end_date',
        'end_time'
    ];

    public function tickets()
    {
        return $this->hasMany(EventTickets::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'imageable');
    }
}
