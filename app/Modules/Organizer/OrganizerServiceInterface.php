<?php

namespace App\Modules\Organizer;

use Illuminate\Support\Collection;

interface OrganizerServiceInterface
{
    public function create(array $organizerData): Collection;
}
