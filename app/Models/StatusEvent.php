<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StatusEvent extends Model
{
    protected $table = 'statusEvent';
    protected $fillable =
        [
            'event_id',
            'user_id',
            'status',
            'created_at',
            'update_at'

        ];


}
