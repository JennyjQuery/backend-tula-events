<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\StatusEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function createReview (Request $request){
        $user = Auth::user();
        $this->validateReview($request);
        $status = Review::create([
            'event_id' => $request->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'date'=>Carbon::now()
        ]);
        return $status;
    }

    public function putLike(Request $request){}

    protected function validateReview(Request $request)
    {
        $rules = [
            'comment' => 'required'
        ];
        $this->validate($request, $rules);
    }

}
