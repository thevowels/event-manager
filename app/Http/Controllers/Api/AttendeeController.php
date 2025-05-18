<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Attendee;
use Illuminate\Http\Request;

use App\Models\Event;

use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;

use Illuminate\Support\Facades\Gate;

class AttendeeController extends Controller
{

    use CanLoadRelationships;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->middleware('throttle:api')->only(['store', 'update', 'destroy']);
    }



    private array $relations = ['user'];
    public function index(Event $event)
    {
        $attendees =  $this->loadRelationships($event->attendees()->latest());
        return AttendeeResource::collection($attendees->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $attendee =  $event->attendees()->create([
            'user_id' => $request->user()->id,
        ]);
        $attendee  = $this->loadRelationships($attendee);

        return AttendeeResource::make($attendee);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event, Attendee $attendee)
    {
        $this->loadRelationships($attendee);
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
        // if(Gate::denies('delete-attendee', [$event, $attendee])) {
        //     return response()->json(['message' => 'You are not the owner to delete this attendee.'], 403);
        // }
        // Gate::authorize('delete-attendee', [$event, $attendee]);
        Gate::authorize('delete', $attendee);
        $attendee->delete();
        return response(status: 204);
    }
    
}
