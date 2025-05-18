<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;


class EventController extends Controller
{

    use CanLoadRelationships;
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected array $relations = ['user', 'attendees', 'attendees.user'];

    public function index(Request $request)
    {

        Gate::authorize('viewAny', Event::class);
        $events= Event::query();

        $this->loadRelationships($events);
        
        return EventResource::collection($events->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);


        $event =  Event::create([...$data, 'user_id' => $request->user()->id]);
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {   
        // return(EventResource::make($event->load('user')));
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
        ]);
        
        $event->update($data);
        return new EventResource($this->loadRelationships($event));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {   
        $deleted_id = $event->id;
        $event->delete();
        return response(status : 204);
    }
}
