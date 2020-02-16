<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = 
    [
       'path',
       'name',
       'type',
       'description',
       'tags'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'users_photos', 'photo_id', 'user_id')
        ->withTimestamps();
    }

    public function post()
    {
        return $this->belongsToMany(Post::class, 'photo_post', 'photo_id', 'post_id');
    }
}
