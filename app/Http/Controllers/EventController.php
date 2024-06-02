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

      // public function store(Request $request, EventTickets $eventTickets)
      // {

      //   $event_title = $request->input('event_title');
      //   $event_location = $request->input('event_location');
      //   $event_description = $request->input('event_description');
      //   $event_address = $request->input('event_address');
      //   $event_refund = $request->input('event_refund');
      //   $event_category = $request->input('event_category');
      //   $event_status = $request->input('event_status');
      //   $event_code = $request->input('event_code');
      //   $event_organizer = $request->input('event_organizer');
      //   $event_start_date = $request->input('event_start_date');
      //   $event_start_time = $request->input('event_start_time');
      //   $event_end_date = $request->input('event_end_date');
      //   $event_end_time = $request->input('event_end_time');
      //   $event_capacity = $request->input('event_capacity');
      //   $ticket_name = $request->input('ticket_name');
      //   $ticket_price = $request->input('ticket_price');
      //   $ticket_quantity = $request->input('ticket_quantity');
      //   $sale_start_date = $request->input('sale_start_date');
      //   $sale_start_time = $request->input('sale_start_time');
      //   $sale_end_date = $request->input('sale_end_date');
      //   $sale_end_time = $request->input('sale_end_time');
      //   $event_publish_or_draft = $request->boolean('event_publish_or_draft');

      //   $event = $this->eventService->create(
      //           $event_title,
      //           $event_location,
      //           $event_description,
      //           $event_address,
      //           $event_refund,
      //           $event_category,
      //           $event_status,
      //           $event_code,
      //           $event_organizer,
      //           $event_start_date,
      //           $event_start_time,
      //           $event_end_date,
      //           $event_end_time,
      //           $event_capacity,
      //           $eventTickets
      //   );
       
      //   if (isset($event['error'])) {
      //     return response()->json(['error' => $event['error']], 422);
      //   }
      //   return response()->json(['message' => 'Event created successfully'], 201);
      // }

      public function store(Request $request, EventTickets $eventTicketsModel)
      {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'event_title' => 'required|string|max:255',
            'event_location' => 'required|string|max:255',
            'event_description' => 'required|string',
            'event_address' => 'required|string|max:255',
            'event_refund' => 'required|string|max:255',
            'event_category' => 'required|string|max:255',
            'event_status' => 'required|string|max:255',
            'event_code' => 'required|string|max:255',
            'event_organizer' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_start_time' => 'required|date_format:H:i',
            'event_end_date' => 'required|date',
            'event_end_time' => 'required|date_format:H:i',
            'event_capacity' => 'required|integer|min:1',
            'ticket_name' => 'required|string|max:255',
            'ticket_price' => 'required|numeric',
            'ticket_quantity' => 'required|integer|min:1',
            'sale_start_date' => 'required|date',
            'sale_start_time' => 'required|date_format:H:i',
            'sale_end_date' => 'required|date',
            'sale_end_time' => 'required|date_format:H:i',
            'event_publish_or_draft' => 'required|boolean',
        ]);

        // Check validation result
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Data passed validation, proceed with event creation
        $eventData = $request->only([
            'event_title',
            'event_location',
            'event_description',
            'event_address',
            'event_refund',
            'event_category',
            'event_status',
            'event_code',
            'event_organizer',
            'event_start_date',
            'event_start_time',
            'event_end_date',
            'event_end_time',
            'event_capacity',
            'ticket_name',
            'ticket_price',
            'ticket_quantity',
            'sale_start_date',
            'sale_start_time',
            'sale_end_date',
            'sale_end_time',
            'event_publish_or_draft'
        ]);

        // Call the service layer to create the event
        $event = $this->eventService->create($eventData, $eventTicketsModel);

        if (isset($event['error'])) {
            return response()->json(['error' => $event['error']], 422);
        }

        return response()->json(['message' => 'Event created successfully'], 201);
      }
}
