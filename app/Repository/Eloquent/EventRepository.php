<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use Illuminate\Support\Collection;
use App\Repository\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Event;


class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    protected $event;

    public function __construct(Event $event)
    {
        parent::__construct($event);
        $this->event = $event;
    }

    public function create(array $eventData, $eventTicketsModel): Collection
    {
       $event_creation = DB::transaction(function () use ($eventData, $eventTicketsModel) {
            $event = $this->event::create([
                'title' => $eventData['event_title'],
                'location' => $eventData['event_location'],
                'description' => $eventData['event_description'],
                'address' => $eventData['event_address'],
                'refund_policy' => $eventData['event_refund'],
                'category' => $eventData['event_category'],
                'status' => $eventData['event_status'],
                'code' => $eventData['event_code'],
                'organizer' => $eventData['event_organizer'],
                'start_date' => $eventData['event_start_date'],
                'start_time' => $eventData['event_start_time'],
                'end_date' => $eventData['event_end_date'],
                'end_time' => $eventData['event_end_time'],
                'capacity' => $eventData['event_capacity'],
            ]);

            $eventId = $event->id;

            $event_ticket = $eventTicketsModel::create([
                'event_id' => $eventId,
                'name' => $eventData['ticket_name'],
                'price' => $eventData['ticket_price'],
                'quantity' => $eventData['ticket_quantity'],
                'sale_start_date' => $eventData['sale_start_date'],
                'sale_start_time' => $eventData['sale_start_time'],
                'sale_end_date' => $eventData['sale_end_date'],
                'sale_end_time' => $eventData['sale_end_time'],
                'publish_or_draft' => $eventData['event_publish_or_draft']
            ]);

            return collect([
                'event' => $event,
                'event_ticket' => $event_ticket
            ]);
       });
       return $event_creation;
    }
}