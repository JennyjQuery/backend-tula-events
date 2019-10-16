<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StatusEvent extends Model
{
    protected $table = 'status_events';
    protected $fillable =
        [
            'event_id',
            'user_id',
            'status'
        ];


}
