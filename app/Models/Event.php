<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        'organizer_id',
        'event_start_date',
        'event_start_time',
        'event_end_date',
        'event_end_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function tickets()
    {
        return $this->hasMany(EventTickets::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'imageable');
    }
}
