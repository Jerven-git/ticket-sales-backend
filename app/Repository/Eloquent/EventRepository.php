<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use Illuminate\Support\Collection;
use App\Repository\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Modules\Media\MediaService;


class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    protected $event;
    protected $mediaService;

    public function __construct(Event $event, MediaService $mediaService)
    {
        parent::__construct($event);
        $this->event = $event;
        $this->mediaService = $mediaService;
    }

    public function create(array $eventData, $eventTicketsModel): Collection
    {
       $event_creation = DB::transaction(function () use ($eventData, $eventTicketsModel) {
            $event = $this->event::create([
                'title' => $eventData['event_title'],
                'event_type' => $eventData['event_type'],
                'location' => $eventData['event_location'],
                'event_link' => $eventData['event_link'],
                'event_note' => $eventData['event_note'],
                'description' => $eventData['event_description'],
                'refund_policy' => $eventData['event_refund'],
                'category' => $eventData['event_category'],
                'event_sub_category' => $eventData['event_sub_category'],
                'code' => $eventData['event_code'],
                'organizer' => $eventData['event_organizer'],
                'start_date' => $eventData['event_start_date'],
                'start_time' => $eventData['event_start_time'],
                'end_date' => $eventData['event_end_date'],
                'end_time' => $eventData['event_end_time'],
            ]);

            foreach ($eventData['event_image'] as $image) {
                $this->mediaService->upload($image, $event);
            }

            $eventId = $event->id;

            $event_ticket = $eventTicketsModel::create([
                'event_id' => $eventId,
                'name' => $eventData['ticket_name'],
                'price' => $eventData['ticket_price'],
                'quantity' => $eventData['ticket_quantity'],
                'ticket_description' => $eventData['ticket_description'],
                'ticket_per_user' => $eventData['ticket_per_user'],
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