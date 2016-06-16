<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;

class UserController extends BaseController
{
    public function getIndex()
    {
        return view('user.index');
    }
}
