<?php

namespace CareSet\CareSetJWTAuthClient\Model;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;


    protected static $admin_domains = ['careset.com'];
    protected $table = 'user';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot','verification_token'
    ];

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'is_activated'=>$this->is_activated,
            'is_admin' => $this->IsAdmin(),
            'apps' => $this->Apps
        ];
    }

    public function IsAdmin()
    {
        return self::IsAdminEmail($this->email);
    }

    public function Apps()
    {
        return $this->belongsToMany('App\App','user_app');
    }

    public static function IsAdminEmail($email)
    {
        $split = explode("@",$email);
        return in_array($split[1],self::$admin_domains);
    }

}
