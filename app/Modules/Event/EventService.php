<?php

namespace App\Modules\Event;

use App\Models\Event;
use App\Repository\EventRepositoryInterface;
use Illuminate\Support\Collection;

class EventService implements EventServiceInterface
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function create(array $eventData, $eventTicketsModel): Collection
    {
        return $this->eventRepository->create($eventData, $eventTicketsModel);
    }
}