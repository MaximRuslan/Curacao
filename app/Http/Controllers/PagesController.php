<?php

namespace App\Http\Controllers;

use App\Models\Cms;

class PagesController extends Controller
{
    public function permissionDenied()
    {
        return view('errors.permission-denied');
    }
}
