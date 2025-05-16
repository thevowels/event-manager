<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use Illuminate\Http\Request;

use App\Models\Event;

use App\Http\Resources\AttendeeResource;

class AttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $attendees =  $event->attendees()->latest();
        return AttendeeResource::collection($attendees->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee =  $event->attendees()->create([
            'user_id' => 1,
        ]);

        return AttendeeResource::make($attendee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        return AttendeeResource::make($attendee);
    }   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event, Attendee $attendee)
    {
        $data = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $attendee->update($data);
        return AttendeeResource::make($attendee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        $attendee->delete();
        return response(status: 204);
    }
    
}
