<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     */

    public function createEvent(Request $request)
    {
        $this->validateEvent($request);
        $user = User::first();


        $event = Event::create([
            'name' => $request->name,
            'place' => $request->place,
            'geolocation' => $request->geolocation,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'type' => $request->type,
            'lat' => $this->getLatCoordinates($request->geolocation),
            'lon' => $this->getLonCoordinates($request->geolocation),
            'description' => $request->description,
            'image' => $request->image,
            'autorization' => $request->autorization
        ]);
        $events_users = Event_Users::create([
            'user_id' => $user->id,
            'event_id' => $request->id

        ]);
        return $event;
    }

    public function updateEvent(Request $request)
    {
        $this->validateEvent($request);
        $user = User::first();
        $event = UserEvents::find($request->id);
        if (!$event) {
            abort(404);
        }
        if ($event->user_id != $user->id) {
            abort(403);
        }
        if ($event->url) {
            abort(409);
        }
        $event = Event::create([
            'name' => $request->name,
            'place' => $request->place,
            'geolocation' => $request->geolocation,
            'date_from' => $user->date_from,
            'date_to' => $request->date_to,
            'type' => $request->type,
            'lat' => $this->getLatCoordinates($request->geolocation),
            'lon' => $this->getLonCoordinates($request->geolocation),
            'description' => $request->description,
            'image' => $request->image,
            'autorization' => $request->autorization
        ]);
        return $event;

    }

    public function getEvent(Request $request)
    {
    }

    public function updateStatusEvent(Request $request)
    {
    }

    protected function validateEvent(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'place' => 'required|string',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
            'type' => 'required|string',
            'lat' => 'nullable',
            'lon' => 'nullable',
            'description' => 'required',
            'image' => 'nullable|string',
            'autorization' => 'required'
        ];
        $this->validate($request, $rules);
    }
    protected function getLatCoordinates($geolocation){

        $data = YaGeo::make()->setQuery(`$geolocation`)->load();
        return $lat = $data->getResponse()->getLatitude();
    }
    protected function getLonCoordinates($geolocation){

        $data = YaGeo::make()->setQuery(`$geolocation`)->load();
        return $lon = $data->getResponse()->getLongitude();
    }
}
