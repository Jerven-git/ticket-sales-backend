<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organizer;
use Illuminate\Support\Facades\Validator;
use App\Modules\Organizer\OrganizerServiceInterface;

class OrganizerController extends Controller
{
    protected OrganizerServiceInterface $organizerService;

    public function __construct(OrganizerServiceInterface $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    public function index()
    {
        $organizers = $this->organizerService->getAll();

        return response()->json(['data' => $organizers], 200);
    }

    public function show(int $id)
    {
        $organizer = $this->organizerService->findById($id);

        if (!$organizer) {
            return response()->json(['error' => 'Organizer not found'], 404);
        }

        return response()->json(['data' => $organizer], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organizer_name' => 'required|string|max:255',
            'organizer_website' => 'nullable|string|max:255',
            'organizer_bio' => 'nullable|string',
            'organizer_facebook_link' => 'nullable|string|max:255',
            'organizer_twitter_link' => 'nullable|string|max:255',
            'organizer_instagram_link' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'organizer_photo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $organizerData = $request->only([
            'organizer_name',
            'organizer_website',
            'organizer_bio',
            'organizer_facebook_link',
            'organizer_twitter_link',
            'organizer_instagram_link',
            'status',
            'organizer_photo'
        ]);

        if ($request->hasFile('organizer_photo')) {
            $organizerData['organizer_photo'] = $request->file('organizer_photo');
        }

        $organizer = $this->organizerService->create($organizerData);

        if (isset($organizer['error'])) {
            return response()->json(['error' => $organizer['error']], 422);
        }

        return response()->json(['message' => 'Organizer created successfully', 'data' => $organizer], 201);
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'organizer_name' => 'sometimes|string|max:255',
            'organizer_website' => 'nullable|string|max:255',
            'organizer_bio' => 'nullable|string',
            'organizer_facebook_link' => 'nullable|string|max:255',
            'organizer_twitter_link' => 'nullable|string|max:255',
            'organizer_instagram_link' => 'nullable|string|max:255',
            'status' => 'sometimes|boolean',
            'organizer_photo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $organizerData = $request->only([
            'organizer_name',
            'organizer_website',
            'organizer_bio',
            'organizer_facebook_link',
            'organizer_twitter_link',
            'organizer_instagram_link',
            'status',
            'organizer_photo'
        ]);

        if ($request->hasFile('organizer_photo')) {
            $organizerData['organizer_photo'] = $request->file('organizer_photo');
        }

        $organizer = $this->organizerService->update($id, $organizerData);

        return response()->json(['message' => 'Organizer updated successfully', 'data' => $organizer], 200);
    }

    public function destroy(int $id)
    {
        $this->organizerService->delete($id);

        return response()->json(['message' => 'Organizer deleted successfully'], 200);
    }
}
