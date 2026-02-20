<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    /**
     * Convert time string (e.g. "01:01 PM", "13:01", "1:01 pm") to MySQL TIME format "HH:MM:SS"
     */
    private function parseTime(?string $time): ?string
    {
        if (!$time) return null;
        $parsed = strtotime($time);
        if ($parsed === false) return null;
        return date('H:i:s', $parsed);
    }

    public function create(array $eventData, $eventTicketsModel): Collection
    {
       $event_creation = DB::transaction(function () use ($eventData, $eventTicketsModel) {
            $event = $this->event::create([
                'user_id' => $eventData['user_id'],
                'event_title' => $eventData['event_title'],
                'event_type' => $eventData['event_type'],
                'event_location' => $eventData['event_location'] ?? null,
                'event_link' => $eventData['event_link'] ?? null,
                'event_note' => $eventData['event_note'] ?? null,
                'event_description' => $eventData['event_description'],
                'event_refund' => $eventData['event_refund'],
                'event_category' => $eventData['event_category'],
                'event_sub_category' => $eventData['event_sub_category'],
                'event_code' => $eventData['event_code'] ?? null,
                'organizer_id' => $eventData['organizer_id'],
                'event_start_date' => $eventData['event_start_date'],
                'event_start_time' => $this->parseTime($eventData['event_start_time']),
                'event_end_date' => $eventData['event_end_date'],
                'event_end_time' => $this->parseTime($eventData['event_end_time']),
            ]);

            if (isset($eventData['event_image'])) {
                if (is_array($eventData['event_image'])) {
                    foreach ($eventData['event_image'] as $image) {
                        $this->mediaService->upload($image, $event);
                    }
                } else {
                    $this->mediaService->upload($eventData['event_image'], $event);
                }
            }

            $eventId = $event->id;

            $event_ticket = $eventTicketsModel::create([
                'event_id' => $eventId,
                'ticket_name' => $eventData['ticket_name'],
                'ticket_price' => $eventData['ticket_price'],
                'ticket_quantity' => $eventData['ticket_quantity'],
                'ticket_description' => $eventData['ticket_description'] ?? '',
                'ticket_per_user' => $eventData['ticket_per_user'],
                'sale_start_date' => $eventData['sale_start_date'] ?? null,
                'sale_start_time' => $this->parseTime($eventData['sale_start_time'] ?? null),
                'sale_end_date' => $eventData['sale_end_date'] ?? null,
                'sale_end_time' => $this->parseTime($eventData['sale_end_time'] ?? null),
                'event_publish_or_draft' => $eventData['event_publish_or_draft'],
                'remaining_ticket' => $eventData['ticket_quantity'],
            ]);

            return collect([
                'event' => $event,
                'event_ticket' => $event_ticket
            ]);
       });
       return $event_creation;
    }

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->event::with(['tickets', 'media', 'organizer']);

        if (isset($filters['category'])) {
            $query->where('event_category', $filters['category']);
        }

        if (isset($filters['sub_category'])) {
            $query->where('event_sub_category', $filters['sub_category']);
        }

        if (isset($filters['organizer'])) {
            $query->where('organizer_id', $filters['organizer']);
        }

        if (isset($filters['upcoming']) && $filters['upcoming']) {
            $query->where('event_start_date', '>=', now()->toDateString());
        }

        return $query->orderBy('event_start_date', 'asc')->paginate($perPage);
    }

    public function findById(int $id): ?Event
    {
        return $this->event::with(['tickets', 'media', 'organizer'])->findOrFail($id);
    }

    public function update(int $id, array $eventData): Event
    {
        return DB::transaction(function () use ($id, $eventData) {
            $event = $this->event::findOrFail($id);

            $event->update([
                'event_title' => $eventData['event_title'] ?? $event->event_title,
                'event_type' => $eventData['event_type'] ?? $event->event_type,
                'event_location' => $eventData['event_location'] ?? $event->event_location,
                'event_link' => $eventData['event_link'] ?? $event->event_link,
                'event_note' => $eventData['event_note'] ?? $event->event_note,
                'event_description' => $eventData['event_description'] ?? $event->event_description,
                'event_refund' => $eventData['event_refund'] ?? $event->event_refund,
                'event_category' => $eventData['event_category'] ?? $event->event_category,
                'event_sub_category' => $eventData['event_sub_category'] ?? $event->event_sub_category,
                'event_code' => $eventData['event_code'] ?? $event->event_code,
                'organizer_id' => $eventData['organizer_id'] ?? $event->organizer_id,
                'event_start_date' => $eventData['event_start_date'] ?? $event->event_start_date,
                'event_start_time' => isset($eventData['event_start_time']) ? $this->parseTime($eventData['event_start_time']) : $event->event_start_time,
                'event_end_date' => $eventData['event_end_date'] ?? $event->event_end_date,
                'event_end_time' => isset($eventData['event_end_time']) ? $this->parseTime($eventData['event_end_time']) : $event->event_end_time,
            ]);

            if (isset($eventData['event_image'])) {
                if (is_array($eventData['event_image'])) {
                    foreach ($eventData['event_image'] as $image) {
                        $this->mediaService->upload($image, $event);
                    }
                } else {
                    $this->mediaService->upload($eventData['event_image'], $event);
                }
            }

            return $event->fresh(['tickets', 'media', 'organizer']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $event = $this->event::findOrFail($id);
            return $event->delete();
        });
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->event::with(['tickets', 'media', 'organizer'])
            ->where('event_title', 'LIKE', "%{$query}%")
            ->orWhere('event_description', 'LIKE', "%{$query}%")
            ->orWhere('event_category', 'LIKE', "%{$query}%")
            ->orWhereHas('organizer', function ($q) use ($query) {
                $q->where('organizer_name', 'LIKE', "%{$query}%");
            })
            ->orWhere('event_location', 'LIKE', "%{$query}%")
            ->orderBy('event_start_date', 'asc')
            ->paginate($perPage);
    }
}
