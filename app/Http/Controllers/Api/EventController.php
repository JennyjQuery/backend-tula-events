<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RatingReviews;
use App\Models\Review;
use App\Models\StatusEvent;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Services\Yandex\Api;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     */

    public function createEvent(Request $request)
    {
        //Insert into table "events"
        $this->validateEvent($request);
        $user = Auth::user();
        $event = Event::create([
            'name' => $request->name,
            'place' => $request->place,
            'geolocation' => $request->geolocation,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'type' => $request->type,
            //проверить существование координат в Response,иначе вернуть null
            'lat' => $this->getLatCoordinates($request->geolocation),
            'lon' => $this->getLonCoordinates($request->geolocation),
            'description' => $request->description,
            'image' => $request->image,
            'autorization' => $request->autorization,
            'organizer_id' => $user->id,
        ]);
        return $event;
    }

    public function updateEvent(Request $request)
    {
        $user_id = Auth::user()->id;
        $event = Event::where('id', $request->id)->firstOrFail();
        if ($user_id != $event->organizer_id) {
            abort(403);
        }
        $this->validateEvent($request);
        foreach ($request->only([
            'name',
            'place',
            'geolocation',
            'date_from',
            'date_to',
            'type',
            'description',
            'image',
            'autorization'
        ]) as $key => $value) {
            $event->{$key} = $value;
        }
        $event->save();
        return $event;
    }

    /* public function getAuthUserEvent(Request $request)
     {
         $user_id = Auth::user()->id;
         //get array "event_id" for user

         $userEventsId = EventsUsers::whereHas('user_id', function($q,$user_id){
             $q->where('user_id', '=', $user_id);
         })->get();

         print_r($userEventsId);
         die();
         $userEvents = EventsUsers::with(['posts' => function ($q) {
             $q->orderBy('created_at', 'desc');
         }])->paginate(10);
         foreach ($userEventsId as $userEventId)
         return Event::where('id', $userEventId)
             ->paginate(20);

     }*/

    public function createStatusEvent(Request $request)
    {
        $user = Auth::user();
        $status = StatusEvent::create([
            'event_id' => $request->id,
            'user_id' => $user->id,
            'status' => $request->status
        ]);
        return $status;
    }

    public function getMoreInformation(Request $request)
    {
        return
            Event::where('id', $request->id)
                ->with('reviews')
                ->first();
    }

    public function statisticEvent(Request $request)
    {
        $user_id = Auth::user()->id;
        $event = Event::where('id', $request->id)->firstOrFail();
        if ($user_id != $event->organizer_id) {
            abort(403);
        }
        $participants = StatusEvent::where('event_id', $request->id)->get();
        $yes = count($participants->where('status', 1));
        $no = count($participants->where('status', 0));
        $perhaps = count($participants->where('status', 2));
        return ['yes' => $yes, 'no' => $no, 'perhaps' => $perhaps];
    }

    public function getEventsOnMaimPage(Request $request)
    {
      /*  if ($request->type) {
            $posts->where('type', $request->type);
        }
        if ($request->provider_id) {
            $posts->where('provider_id', $request->provider_id);
        }
        if ($request->category_id) {
            $posts->where('category_id', $request->category_id);
        }*/
    }

    public function getEventsOnMap(Request $request)
    {

    }

    protected function validateEvent(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'place' => 'required|string',
            'geolocation' => 'required|string',
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

    protected function getLatCoordinates($geolocation)
    {
        $api = new Api('1.x');
        $api->setToken('afa8469a-c05d-432c-82da-5c9eb0a16360');
        $api
            ->setQuery($geolocation)
            ->load();
        $response = $api->getResponse();
        return $lat = $response->getLatitude();

    }

    protected function getLonCoordinates($geolocation)
    {
        $api = new Api('1.x');
        $api->setToken('afa8469a-c05d-432c-82da-5c9eb0a16360');
        $api
            ->setQuery($geolocation)
            ->load();
        return $lon = $api->getResponse()->getLongitude();
    }
}
