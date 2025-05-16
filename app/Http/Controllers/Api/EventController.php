<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $relations = ['user', 'attendees', 'attendees.user'];
        $events= Event::query();
        foreach($relations as $relation) {
            if($this->shouldIncludeRelation($relation)) {
                $events->with($relation);
            }
        }
        
        return EventResource::collection($events->paginate());
    }

    protected function shouldIncludeRelation(string $relation): bool
    {
        $include = request()->query('include');
        if(!$include) return false;
        $relations = array_map('trim', explode(',', $include));
        return in_array($relation, $relations);
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


        $event =  Event::create([...$data, 'user_id' => 1]);
        return EventResource::make($event);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return EventResource::make($event->load('user','attendees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
        ]);
        
        $event->update($data);
        return $event;
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
