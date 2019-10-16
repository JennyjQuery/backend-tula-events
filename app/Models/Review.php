<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'comment',
        'date'
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
