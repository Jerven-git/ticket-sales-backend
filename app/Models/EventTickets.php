<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTickets extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_name',
        'ticket_price',
        'ticket_quantity',
        'ticket_per_user',
        'ticket_description',
        'sale_start_date',
        'sale_start_time',
        'sale_end_date',
        'sale_end_time',
        'event_publish_or_draft',
        'remaining_ticket',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'event_ticket_id');
    }
}
