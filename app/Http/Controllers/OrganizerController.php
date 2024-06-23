<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizer;
use Illuminate\Support\Facades\Validator;
use App\Modules\Organizer\OrganizerServiceInterface;

class OrganizerController extends Controller
{
    /**
     * Organizer Module
     * @var OrganizerServiceInterface $organizerService
     */
    protected OrganizerServiceInterface $organizerService;

     /**
      * Organizer Controller Constructor
      *
      * @param OrganizerServiceInterface $organizerService
      */
      public function __construct(OrganizerServiceInterface $organizerService)
      {
        $this->organizerService = $organizerService;
      }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'required|string|max:255',
            'organizer_website' => 'required|string|max:255',
            'organizer_bio' => 'required|string',
            'organizer_facebook_link' => 'required|string|max:255',
            'organizer_twitter_link' => 'required|string|max:255',
            'organizer_instagram_link' => 'required|string|max:255',
            'organizer_status' => 'required|boolean',
            'organizer_photo' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $organizerData = $request->only([
            'organizer_name',
            'organizer_email',
            'organizer_website',
            'organizer_bio',
            'organizer_facebook_link',
            'organizer_twitter_link',
            'organizer_instagram_link',
            'organizer_status',
            'organizer_photo'
        ]);

        if ($request->hasFile('organizer_photo')) {
            $organizerData['organizer_photo'] = $request->file('organizer_photo');
        }

        $organizer = $this->organizerService->create($organizerData);

        if (isset($organizer['error'])) {
            return response()->json(['error' => $organizer['error']], 422);
        }

        return response()->json(['message' => 'Organizer created successfully'], 201);
    }
}
