<?php

namespace App\Modules\Event;

use App\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EventServiceInterface
{
    public function create(array $eventData, $eventTicketsModel): Collection;

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Event;

    public function update(int $id, array $eventData): Event;

    public function delete(int $id): bool;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}
