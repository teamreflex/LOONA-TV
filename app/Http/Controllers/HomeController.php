<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Home');
    }
}
