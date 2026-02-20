<?php

namespace App\Modules\Organizer;

use App\Models\Organizer;
use App\Repository\OrganizerRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class OrganizerService implements OrganizerServiceInterface
{
    protected OrganizerRepositoryInterface $organizerRepository;

    public function __construct(OrganizerRepositoryInterface $organizerRepository)
    {
        $this->organizerRepository = $organizerRepository;
    }

    public function create(array $organizerData): Collection
    {
        return $this->organizerRepository->create($organizerData);
    }

    public function getAll(): EloquentCollection
    {
        return $this->organizerRepository->getAll();
    }

    public function findById(int $id): ?Organizer
    {
        return $this->organizerRepository->findById($id);
    }

    public function update(int $id, array $organizerData): Organizer
    {
        return $this->organizerRepository->update($id, $organizerData);
    }

    public function delete(int $id): bool
    {
        return $this->organizerRepository->delete($id);
    }
}
