<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller as BaseController;
use Auth;

class SiteController extends BaseController
{
    public function getIndex()
    {
        return view('welcome');
    }
}
