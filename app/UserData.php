<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserData extends Authenticatable
{
    protected $table = 'users_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'value', 'user_id',
    ];

    /**
     * Get the user that owns the data.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
