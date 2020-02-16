<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = "post";
    protected $fillable = [
        'id_user',
        'title',
        'text',
        'marked_friends',
        'created_date'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function photo()
    {
        return $this->belongsToMany(Photo::class, 'photo_post', 'post_id', 'photo_id');
    }
}
