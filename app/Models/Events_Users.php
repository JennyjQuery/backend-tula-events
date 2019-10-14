<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events_Users extends Model
{
    protected $table = 'events_users';
    protected $fillable = [
        'user_id',
        'event_id'
    ];
}
