<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $table = 'events';
    protected $fillable = [
        'name',
        'place',
        'date_from',
        'date_to',
        'type',
        'lat',
        'lon',
        'description',
        'image',
        'autorization'
    ];


}
