<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RatingReviews;
use App\Models\Review;
use App\Models\StatusEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function createReview (Request $request){
        $userId = Auth::id();
        $id = Auth::id();
        $this->validateReview($request);
        if (Auth::check())
        {
            $status = Review::create([
                'event_id' => $request->id,
                'user_id' =>  $id,
                //'user_id' =>  Auth::id(),
                //'user_id' =>  Auth::user()->id,
                'comment' => $request->comment
            ]);
        }

        return $status;
    }

    public function putLike(Request $request){
        $user = Auth::user();
        $status = RatingReviews::create([
            'review_id' => $request->review_id,
            'user_id' => $user->id,
            'like' => $request->like,
            'dislike' => $request->dislike
        ]);
        return $status;
    }

    protected function validateReview(Request $request)
    {
        $rules = [
            'comment' => 'required'
        ];
        $this->validate($request, $rules);
    }

}
