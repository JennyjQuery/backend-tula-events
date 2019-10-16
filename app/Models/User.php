<?php

namespace App\Models;

use DateTime;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\PersonalAccessTokenResult;
use Carbon;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens {
        createToken as traitCreateToken;
    }
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guard_name = 'api';

    protected $fillable = [
        'email',
        'password',
        'name',
        'phone',
        'surname',
        'date_of_birth',
        'sex',
        'avatar',
        'name_organization'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function createToken($name, array $scopes = []): PersonalAccessTokenResult
    {
        if (!$scopes) {
            $scopes = $this
                ->getAllPermissions()
                ->pluck('name')
                ->toArray();
        }

        return $this->traitCreateToken($name, $scopes);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function organizerEvents($past,$now)
    {
/*        print_r($now);
        $events = $this->hasMany(Event::class, 'organizer_id')->pluck('date_to');
        //print_r(new DateTime('today'));
        print_r($events);
        die();*/
        if ($past) {
            return $this->hasMany(Event::class, 'organizer_id')->where('date_to' , '<=' , $now);

        } else return $this->hasMany(Event::class, 'organizer_id')->where('date_to','>',$now);
    }

    public function participantEvents($past,$now)
    {
        if($past){
            return $this->belongsToMany(Event::class, 'status_events')->withTimestamps()->where('date_to'< $now);
        }else return $this->belongsToMany(Event::class, 'status_events')->withTimestamps()->where('date_to'>= $now);
    }

    public function role()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id');
    }

    public function reviews(){
        return $this->hasMany(Review::class)->with('reviews');
    }

}
