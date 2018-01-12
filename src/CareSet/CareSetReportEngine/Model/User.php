<?php

namespace CareSet\CareSetJWTAuthClient\Model;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'auth.user';

    protected $fillable = [
        'name','email','is_admin','last_token'
    ];

    protected $hidden = [
        'password', 'remember_token','last_token'
    ];



}

