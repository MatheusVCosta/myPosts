<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = "users";
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'is_actived',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //////////////////////////////////////////////////
    ///////////////////SCOPES/////////////////////////
    //////////////////////////////////////////////////
    public function scopeIsActived($query)
    {
        return $query->whereNull('deleted_at')->where('is_actived', 1);
    }
    public function scopeUsersIsNotnull($query)
    {
        return $query->where('created_by', '!=', null);
    } 

    public function scopeCountMember($query, $user_id)
    {
        return $query->where('id_user', '=', $user_id)->count();
    }

    //////////////////////////////////////////////////
    ///////////////////ASSOCIATIONS///////////////////
    //////////////////////////////////////////////////
    public function aboutUser()
    {
        return $this->hasMany(AboutUser::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function groupCreate()
    {
        return $this->hasMany(Group::class, 'created_by', 'id');
    }

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'users_photos', 'user_id', 'photo_id')
        ->withTimestamps();
    }
    
    public function profilePhoto()
    {
        return $this->belongsToMany(Photo::class, "profile_photo", "user_id", "photo_id")
        ->withPivot('is_profile')
        ->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'users_groups', 'user_id', 'group_id');
    }
}
