<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'isd_code',
        'phone',
        'country',
        'social_id',
        'social_type',
        'is_phone_verified',
        'status',
        'last_login',
        'total_login',
        'isOnline',
        'is_email_receive_allow'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['fullname'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d H:i:s') : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function whiteboards()
    {
        return $this->belongsToMany(Whiteboard::class);
    }

    public static function laratablesCustomAction($user)
    {
        return view('admin.users.action', compact('user'))->render();
    }

    public function user_detail(){
        return $this->hasOne(UserDetail::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'createdBy', 'id');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function getFullnameAttribute() {
        return $this->firstname.' '.$this->lastname;
    }

    public function followers()
    {
        return $this->hasMany(Follower::class);
    }

    public function followings()
    {
        return $this->hasMany(Follower::class, 'follow_id', 'id');
    }

    public function company(){
        return $this->hasOne(Company::class);
    }

}
