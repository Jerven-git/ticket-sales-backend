<?php

namespace App\Repository;

use Illuminate\Support\Collection;

interface EventRepositoryInterface
{
    public function create(array $eventData, $eventTicketsModel): Collection;
}