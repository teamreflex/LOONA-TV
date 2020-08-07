<?php

namespace App\Http\Controllers;

use App\Arc;
use Inertia\Inertia;
use Inertia\Response;

class ArcController extends Controller
{

    /**
     * Show the application index.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Home', [
            'arcs' => Arc::all(),
        ]);
    }

    /**
     * Show a single arc.
     *
     * @param int $arc
     * @return Response
     */
    public function show(int $arc): Response
    {
        return Inertia::render('Arc', [
            'arc' => Arc::with('episodes')->find($arc),
        ]);
    }
}
