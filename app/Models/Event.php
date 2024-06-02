<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_title',
        'event_location',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'event_description',
        'event_address',
        'event_refund',
        'event_category',
        'event_status',
        'event_code',
        'event_organizer',
        'event_capacity'
    ];

    public function tickets()
    {
        return $this->hasMany(EventTickets::class);
    }
}
