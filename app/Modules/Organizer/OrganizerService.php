<?php

namespace App\Modules\Organizer;
use App\Repository\OrganizerRepositoryInterface;
use Illuminate\Support\Collection;

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
}