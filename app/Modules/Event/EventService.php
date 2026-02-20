<?php

namespace App\Modules\Event;

use App\Models\Event;
use App\Repository\EventRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->eventRepository->getAll($filters, $perPage);
    }

    public function findById(int $id): ?Event
    {
        return $this->eventRepository->findById($id);
    }

    public function update(int $id, array $eventData): Event
    {
        return $this->eventRepository->update($id, $eventData);
    }

    public function delete(int $id): bool
    {
        return $this->eventRepository->delete($id);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->eventRepository->search($query, $perPage);
    }
}
