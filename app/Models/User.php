<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'userid',
        'address',
        'is_active',
        'lat',
        'lng',
        'id_card_number',
        'code_otp',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'id_google'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function engineer()
    {
        return $this->hasOne('App\Models\Engineer');
    }

    public function chatrooms()
    {
        return $this->hasMany('App\Models\Chatroom','user_1','id');
    }

    public function chats()
    {
        return $this->hasMany('App\Models\Chat','from','id');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province','province_id','id');
    }

    public function regency()
    {
        return $this->belongsTo('App\Models\Regency','regency_id','id');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District','district_id','id');
    }

    public function village()
    {
        return $this->belongsTo('App\Models\Village','village_id','id');
    }
}
