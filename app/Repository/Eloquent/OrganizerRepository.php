<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use App\Repository\OrganizerRepositoryInterface;
use App\Models\Organizer;
use App\Modules\Media\MediaService;
use Illuminate\Support\Collection;
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
                'organizer_email' => $organizerData['organizer_email'],
                'organizer_website' => $organizerData['organizer_website'],
                'organizer_bio' => $organizerData['organizer_bio'],
                'organizer_facebook_link' => $organizerData['organizer_facebook_link'],
                'organizer_twitter_link' => $organizerData['organizer_twitter_link'],
                'organizer_instagram_link' => $organizerData['organizer_instagram_link'],
                'organizer_status' => $organizerData['organizer_status'],
            ]);

            if (isset($organizerData['organizer_photo']) && is_array($organizerData['organizer_photo'])) {
                foreach ($organizerData['organizer_photo'] as $image) {
                    $this->mediaService->upload($image, $organizer);
                }
            }

            return collect([
                'organizer' => $organizer
            ]);
        });
        return $organizer_creation;
    }
}
