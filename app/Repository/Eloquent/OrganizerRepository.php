<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use App\Repository\OrganizerRepositoryInterface;
use App\Models\Organizer;
use App\Modules\Media\MediaService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class OrganizerRepository extends BaseRepository implements OrganizerRepositoryInterface
{
    protected $organizer;
    protected $mediaService;

    public function __construct(Organizer $organizer, MediaService $mediaService)
    {
        parent::__construct($organizer);
        $this->organizer = $organizer;
        $this->mediaService = $mediaService;
    }

    public function create(array $organizerData): Collection
    {
        $organizer_creation = DB::transaction(function () use ($organizerData) {
            $organizer = $this->organizer::create([
                'organizer_name' => $organizerData['organizer_name'],
                'organizer_website' => $organizerData['organizer_website'] ?? '',
                'organizer_bio' => $organizerData['organizer_bio'] ?? '',
                'organizer_facebook_link' => $organizerData['organizer_facebook_link'] ?? '',
                'organizer_twitter_link' => $organizerData['organizer_twitter_link'] ?? '',
                'organizer_instagram_link' => $organizerData['organizer_instagram_link'] ?? '',
                'status' => $organizerData['status'],
            ]);

            if (isset($organizerData['organizer_photo'])) {
                if (is_array($organizerData['organizer_photo'])) {
                    foreach ($organizerData['organizer_photo'] as $image) {
                        $this->mediaService->upload($image, $organizer);
                    }
                } else {
                    $this->mediaService->upload($organizerData['organizer_photo'], $organizer);
                }
            }

            return collect([
                'organizer' => $organizer
            ]);
        });
        return $organizer_creation;
    }

    public function getAll(): EloquentCollection
    {
        return $this->organizer::with('media')->where('status', true)->get();
    }

    public function findById(int $id): ?Organizer
    {
        return $this->organizer::with('media')->findOrFail($id);
    }

    public function update(int $id, array $organizerData): Organizer
    {
        return DB::transaction(function () use ($id, $organizerData) {
            $organizer = $this->organizer::findOrFail($id);

            $organizer->update([
                'organizer_name' => $organizerData['organizer_name'] ?? $organizer->organizer_name,
                'organizer_website' => $organizerData['organizer_website'] ?? $organizer->organizer_website,
                'organizer_bio' => $organizerData['organizer_bio'] ?? $organizer->organizer_bio,
                'organizer_facebook_link' => $organizerData['organizer_facebook_link'] ?? $organizer->organizer_facebook_link,
                'organizer_twitter_link' => $organizerData['organizer_twitter_link'] ?? $organizer->organizer_twitter_link,
                'organizer_instagram_link' => $organizerData['organizer_instagram_link'] ?? $organizer->organizer_instagram_link,
                'status' => $organizerData['status'] ?? $organizer->status,
            ]);

            if (isset($organizerData['organizer_photo'])) {
                if (is_array($organizerData['organizer_photo'])) {
                    foreach ($organizerData['organizer_photo'] as $image) {
                        $this->mediaService->upload($image, $organizer);
                    }
                } else {
                    $this->mediaService->upload($organizerData['organizer_photo'], $organizer);
                }
            }

            return $organizer->fresh('media');
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $organizer = $this->organizer::findOrFail($id);
            return $organizer->delete();
        });
    }
}
