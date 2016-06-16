<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller as BaseController;

class SiteController extends BaseController
{
    public function getIndex()
    {
        return view('welcome');
    }
}
