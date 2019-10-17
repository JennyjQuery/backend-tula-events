<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingReviews extends Model
{
    protected $table = 'rating_reviews';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'review_id',
        'like',
        'dislike'
    ];
}
