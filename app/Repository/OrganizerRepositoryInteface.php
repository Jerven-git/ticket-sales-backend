<?php

namespace App\Repository;

use Illuminate\Support\Collection;

interface OrganizerRepositoryInterface
{
    public function create(array $organizerData): Collection;
}
