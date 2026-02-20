<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Event\EventServiceInterface;
use App\Models\Event;
use App\Models\EventTickets;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    protected EventServiceInterface $eventService;

    public function __construct(EventServiceInterface $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['category', 'sub_category', 'organizer', 'upcoming']);
        $perPage = (int) $request->input('per_page', 15);

        $events = $this->eventService->getAll($filters, $perPage);

        return response()->json(['data' => $events], 200);
    }

    public function show(int $id)
    {
        $event = $this->eventService->findById($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json(['data' => $event], 200);
    }

    public function store(Request $request, EventTickets $eventTicketsModel)
    {
        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'event_type' => 'required|boolean',
            'event_description' => 'required|string',
            'event_refund' => 'required|string|max:255',
            'event_category' => 'required|string|max:255',
            'event_sub_category' => 'required|string|max:255',
            'organizer_id' => 'required|integer|exists:organizers,id',
            'event_start_date' => 'required|date',
            'event_start_time' => 'required|date_format:h:i A,H:i:s,H:i',
            'event_end_date' => 'required|date',
            'event_end_time' => 'required|date_format:h:i A,H:i:s,H:i',
            'event_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_name' => 'required|string|max:255',
            'ticket_price' => 'required|numeric',
            'ticket_quantity' => 'required|integer|min:1',
            'ticket_per_user' => 'required|integer|min:1',
            'event_publish_or_draft' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $eventData = $request->only([
            'event_title',
            'event_type',
            'event_location',
            'event_link',
            'event_note',
            'event_description',
            'event_refund',
            'event_category',
            'event_sub_category',
            'event_code',
            'organizer_id',
            'event_image',
            'event_start_date',
            'event_start_time',
            'event_end_date',
            'event_end_time',
            'ticket_name',
            'ticket_price',
            'ticket_quantity',
            'ticket_description',
            'ticket_per_user',
            'sale_start_date',
            'sale_start_time',
            'sale_end_date',
            'sale_end_time',
            'event_publish_or_draft'
        ]);

        $eventData['user_id'] = $request->user()->id;

        if ($request->hasFile('event_image')) {
            $eventData['event_image'] = $request->file('event_image');
        }

        $event = $this->eventService->create($eventData, $eventTicketsModel);

        if (isset($event['error'])) {
            return response()->json(['error' => $event['error']], 422);
        }

        return response()->json(['message' => 'Event created successfully', 'data' => $event], 201);
    }

    public function update(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $this->authorize('update', $event);

        $validator = Validator::make($request->all(), [
            'event_title' => 'sometimes|string|max:255',
            'event_type' => 'sometimes|boolean',
            'event_description' => 'sometimes|string',
            'event_refund' => 'sometimes|string|max:255',
            'event_category' => 'sometimes|string|max:255',
            'event_sub_category' => 'sometimes|string|max:255',
            'organizer_id' => 'sometimes|integer|exists:organizers,id',
            'event_start_date' => 'sometimes|date',
            'event_start_time' => 'sometimes|date_format:h:i A,H:i:s,H:i',
            'event_end_date' => 'sometimes|date',
            'event_end_time' => 'sometimes|date_format:h:i A,H:i:s,H:i',
            'event_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $eventData = $request->only([
            'event_title',
            'event_type',
            'event_location',
            'event_link',
            'event_note',
            'event_description',
            'event_refund',
            'event_category',
            'event_sub_category',
            'event_code',
            'organizer_id',
            'event_start_date',
            'event_start_time',
            'event_end_date',
            'event_end_time',
        ]);

        if ($request->hasFile('event_image')) {
            $eventData['event_image'] = $request->file('event_image');
        }

        $event = $this->eventService->update($id, $eventData);

        return response()->json(['message' => 'Event updated successfully', 'data' => $event], 200);
    }

    public function destroy(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $this->authorize('delete', $event);

        $this->eventService->delete($id);

        return response()->json(['message' => 'Event deleted successfully'], 200);
    }

    public function myEvents(Request $request)
    {
        $events = Event::with(['tickets', 'media'])
            ->where('user_id', $request->user()->id)
            ->orderBy('event_start_date', 'asc')
            ->paginate(15);

        return response()->json(['data' => $events], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return response()->json(['data' => []], 200);
        }

        $events = $this->eventService->search($query);

        return response()->json(['data' => $events], 200);
    }
}
