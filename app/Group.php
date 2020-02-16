<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = "groups";

    protected $fillable = [ 
        'group_name',
        'is_private',
        'tags',
        'photo_mains',
        'created_date',
        'created_by'
    ];


    // ASSOCIATION //
    public function users()
    {
        return $this->belongsToMany(User::class, 'groups_users', 'group_id', 'user_id')
        ->withPivot(['id'])
        ->withTimestamps();
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
