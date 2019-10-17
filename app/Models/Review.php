<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $pivotColumns = ['comment'];

    protected $fillable = [
        'comment',
        'user_id',
        'event_id'
    ];
    //один ко многим
    public function event(){
        return $this->belongsTo(Event::class);
    }
    //
    public function user(){
        //return $this->
    }

}
