<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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
        'password', 'remember_token',
    ];

    /**
     * Get the data for the user.
     */
    public function data()
    {
        return $this->hasMany('App\UserData');
    }

    /**
     * Set the data for the user.
     * @var string $name
     * @var string $value
     *
     * @return bool $result
     */
    public function set($name, $value)
    {
        return $this->data()->updateOrCreate(
          ['name' => $name],
          [
            'name' => $name,
            'value' => $value,
          ]);
    }

    /**
     * Get the data for the user.
     * @var string $name
     *
     * @return string $value
     */
    public function get($name)
    {
        $data = $this->data()->where(['name' => $name])->first();
        return ($data) ? $data->value:false;
    }
}
