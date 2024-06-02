<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTickets extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_name',
        'ticket_price',
        'ticket_quantity',
        'sale_start_date',
        'sale_start_time',
        'sale_end_date',
        'sale_end_time',
        'event_publish_or_draft',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
