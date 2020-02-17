<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AboutUser extends Model
{
    protected $table = "about_users";
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'user_id',
        'about',
        'phone',
        'mobile_phone',
        'birthday'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
