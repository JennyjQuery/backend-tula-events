<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventsUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getProfile()
    {
        /*        return
                    User::with('user')
                        ->with('events_users')
                        ->with('status_events')
                        ->first();*/
        return
            User::join('events_users', 'users.id', '=', 'events_users.user_id')
                ->join('events', 'events_users.event_id', '=', 'events.id');


    }

    public function updateProfile(Request $request)
    {
        $organizer = Auth::user();
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string',
            'surname' => 'required|string',
            'phone' => 'required|string|unique:users,email',
            'avatar' => 'nullable|string',
            'name_organization' => 'required|string'
        ]);

        foreach ($request->only([
            'email',
            'password',
            'name',
            'surname',
            'phone',
            'avatar',
            'name_organization'
        ]) as $key => $value) {
            $organizer->{$key} = $value;
        }

        $organizer->save();

        /*organizer->categories()->sync($request->get('subcategories'));
        organizer->social_networks()->sync($request->get('social_networks'));

        $baseBranch = organizer->base_branch()->first();
        if ($baseBranch) {
            foreach (
                $request->only([
                    'country_code',
                    'city_id',
                    'address',
                    'zip',
                ]) as $key => $value) {
                $baseBranch->{$key} = $value;
            }
            $baseBranch->save();
        } else {
            $baseBranch = Branch::create([
                'name' => config('chaston.base_branch_name'),
                'country_code' => $request->country_code,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'zip' => $request->zip,
                'provider_id' => organizer->id,
                'is_base' => 1,
            ]);
        }

        Coordinates::set($baseBranch, 'address');*/
    }
    public function getEvent(Request $request)
    {
        $user = Auth::user();
        //get array "event_id" for user
        $userEventsId = User::find($user->id)->events()->pluck('id');
        printf($userEventsId);
        die();
        //$userEventsId=$user->events()->pluck('event_id')->to_array();
/*        $userEventsId = EventsUsers::whereHas('user_id', function($q,$user_id){
            $q->where('user_id', '=', $user_id);
        })->get();*/


        foreach ($userEventsId as $userEventId)
            return Event::where('id', $userEventId)
                ->paginate(20);

    }

}
