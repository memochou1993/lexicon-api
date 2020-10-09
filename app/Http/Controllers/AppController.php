<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AppController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return View
     */
    public function __invoke()
    {
        return view('app');
    }
}
