<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;

class HomeController extends Controller
{
    public function index()
    {
        $sports = Sport::where('is_active', true)->get();
        $events = Event::with(['sport', 'odds'])
            ->where('status', 'upcoming')
            ->orderBy('event_date')
            ->paginate(9);

        return view('home', compact('sports', 'events'));
    }

    public function sport(Sport $sport)
    {
        $events = Event::with(['sport', 'odds'])
            ->where('sport_id', $sport->id)
            ->where('status', 'upcoming')
            ->orderBy('event_date')
            ->paginate(9);

        $sports = Sport::where('is_active', true)->get();

        return view('home', compact('sports', 'events', 'sport'));
    }
}
