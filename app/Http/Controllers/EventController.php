<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Event\EventServiceInterface;
use App\Models\EventTickets;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Event Module
     * @var EventServiceInterface $eventService
     */
     protected EventServiceInterface $eventService;

     /**
      * Event Controller Constructor
      * 
      * @param EventServiceInterface $eventService
      *
      */
      public function __construct(EventServiceInterface $eventService)
      {
        $this->eventService = $eventService;
      }

      public function store(Request $request, EventTickets $eventTicketsModel)
      {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'event_type' => 'required|boolean',
            'event_description' => 'required|string',
            'event_refund' => 'required|string|max:255',
            'event_category' => 'required|string|max:255',
            'event_sub_category' => 'required|string|max:255',
            'event_code' => 'required|string|max:255',
            'event_organizer' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_start_time' => 'required|date_format:H:i',
            'event_end_date' => 'required|date',
            'event_end_time' => 'required|date_format:H:i',
            'event_image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_name' => 'required|string|max:255',
            'ticket_price' => 'required|numeric',
            'ticket_quantity' => 'required|integer|min:1',
            'ticket_per_user' => 'required|integer|min:1',
            'event_publish_or_draft' => 'required|boolean',
        ]);

        // Check validation result
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Data passed validation, proceed with event creation
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
            'event_organizer',
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

        if ($request->hasFile('event_image')) {
            $eventData['event_image'] = $request->file('event_image');
        }

        // Call the service layer to create the event
        $event = $this->eventService->create($eventData, $eventTicketsModel);

        if (isset($event['error'])) {
            return response()->json(['error' => $event['error']], 422);
        }

        return response()->json(['message' => 'Event created successfully'], 201);
      }
}
