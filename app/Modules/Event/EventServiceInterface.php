<?php

namespace App\Modules\Event;

use Illuminate\Support\Collection;

interface EventServiceInterface
{
    public function create(array $eventData, $eventTicketsModel): Collection;
}