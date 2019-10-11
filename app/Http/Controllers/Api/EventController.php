<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Organizer;
class EventController extends Controller
{
    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse | array
     */

    public function createEvent(Request $request)
    {
        print_r($request->all());
        die();
        $this->validateEvent($request);
        $organizer = Organizer::first();
        $event = Event::create([
            'name' => $request->name,
            'place' => $request->place,
            'date_from' => $organizer->date_from,
            'date_to' => $request->date_to,
            'type' => $request->type,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'description' => $request->description,
            'image' => $request->image,
            'autorization' => $request->autorization
        ]);
        $events_users = Event_Users::create([
            'user_id' => $organizer->id,
            'event_id' => $request->id

        ]);
        return $event;
    }

    public function updateEvent(Request $request){
        $this->validateEvent($request);
        $organizer = Organizer::first();
        $event = UserEvents::find($request->id);
        if (!$event){
            abort(404);
        }
        if ($event->user_id != $organizer->id){
            abort(403);
        }
/*        if ($event->url) {
            abort(409);
        }*/

        $event = Event::create([
            'name' => $request->name,
            'place' => $request->place,
            'date_from' => $organizer->date_from,
            'date_to' => $request->date_to,
            'type' => $request->type,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'description' => $request->description,
            'image' => $request->image,
            'autorization' => $request->autorization
        ]);
        return $event;

    }

    public function getEvent(Request $request){}

    public function updateStatusEvent(Request $request){}

    protected function validateEvent(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'place' =>'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'type' => 'required|string',
            'lat' => 'required|double|regex:/^\d+(\.\d{1,2})?$/',
            'lon' => 'required|double|regex:/^\d+(\.\d{1,2})?$/',
            'description' => 'required|text',
            'image' => 'nullable|string',
            'autorization' => 'required|tinyInt'
        ];
        $this->validate($request, $rules);
    }
}
