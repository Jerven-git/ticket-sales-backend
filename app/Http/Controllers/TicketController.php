<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventTickets;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index(int $eventId)
    {
        $tickets = EventTickets::where('event_id', $eventId)->get();

        return response()->json(['data' => $tickets], 200);
    }

    public function store(Request $request, int $eventId)
    {
        $validator = Validator::make($request->all(), [
            'ticket_name' => 'required|string|max:255',
            'ticket_price' => 'required|numeric',
            'ticket_quantity' => 'required|integer|min:1',
            'ticket_per_user' => 'required|integer|min:1',
            'ticket_description' => 'nullable|string',
            'sale_start_date' => 'nullable|date',
            'sale_start_time' => 'nullable',
            'sale_end_date' => 'nullable|date',
            'sale_end_time' => 'nullable',
            'event_publish_or_draft' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $ticketData = $request->only([
            'ticket_name',
            'ticket_price',
            'ticket_quantity',
            'ticket_per_user',
            'ticket_description',
            'sale_start_date',
            'sale_start_time',
            'sale_end_date',
            'sale_end_time',
            'event_publish_or_draft',
        ]);

        $ticketData['event_id'] = $eventId;
        $ticketData['remaining_ticket'] = $ticketData['ticket_quantity'];

        $ticket = EventTickets::create($ticketData);

        return response()->json(['message' => 'Ticket created successfully', 'data' => $ticket], 201);
    }

    public function show(int $eventId, int $ticketId)
    {
        $ticket = EventTickets::where('event_id', $eventId)->findOrFail($ticketId);

        return response()->json(['data' => $ticket], 200);
    }

    public function update(Request $request, int $eventId, int $ticketId)
    {
        $ticket = EventTickets::where('event_id', $eventId)->findOrFail($ticketId);

        $validator = Validator::make($request->all(), [
            'ticket_name' => 'sometimes|string|max:255',
            'ticket_price' => 'sometimes|numeric',
            'ticket_quantity' => 'sometimes|integer|min:1',
            'ticket_per_user' => 'sometimes|integer|min:1',
            'ticket_description' => 'nullable|string',
            'sale_start_date' => 'nullable|date',
            'sale_start_time' => 'nullable',
            'sale_end_date' => 'nullable|date',
            'sale_end_time' => 'nullable',
            'event_publish_or_draft' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $ticketData = $request->only([
            'ticket_name',
            'ticket_price',
            'ticket_quantity',
            'ticket_per_user',
            'ticket_description',
            'sale_start_date',
            'sale_start_time',
            'sale_end_date',
            'sale_end_time',
            'event_publish_or_draft',
        ]);

        $ticket->update($ticketData);

        return response()->json(['message' => 'Ticket updated successfully', 'data' => $ticket->fresh()], 200);
    }

    public function destroy(int $eventId, int $ticketId)
    {
        $ticket = EventTickets::where('event_id', $eventId)->findOrFail($ticketId);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully'], 200);
    }
}
