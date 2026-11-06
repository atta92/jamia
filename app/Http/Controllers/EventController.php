<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->published()
            ->orderByRaw('CASE WHEN starts_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('starts_at')
            ->orderBy('id')
            ->paginate(9);

        return view('events.index', [
            'events' => $events,
            'solidHeader' => true,
        ]);
    }

    public function show(string $slug): View
    {
        $event = Event::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Event::query()
            ->upcoming()
            ->where('id', '!=', $event->id)
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        return view('events.show', [
            'event' => $event,
            'related' => $related,
            'solidHeader' => true,
        ]);
    }
}
