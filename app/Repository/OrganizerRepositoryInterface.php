<?php

namespace App\Repository;

use App\Models\Organizer;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface OrganizerRepositoryInterface
{
    public function create(array $organizerData): Collection;

    public function getAll(): EloquentCollection;

    public function findById(int $id): ?Organizer;

    public function update(int $id, array $organizerData): Organizer;

    public function delete(int $id): bool;
}
