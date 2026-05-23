<?php

namespace App\Modules\Calendar\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Calendar\Models\Event;
use Illuminate\Http\Request;
use App\Modules\Calendar\Requests\StoreEventRequest;

/**
 * @group Calendar
 *
 * APIs for managing calendar events
 */
class EventController extends Controller
{
    /**
     * List all events
     */
    public function index(Request $request)
    {
        $query = Event::query();

        if ($month = $request->month) $query->whereMonth('start_date', $month);
        if ($year = $request->year) $query->whereYear('start_date', $year);
        if ($type = $request->type) $query->where('type', $type);

        return $this->success($query->orderBy('start_date')->get());
    }

    /**
     * Create a new event
     */
    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());
        return $this->created($event, 'Event created');
    }

    /**
     * Update an event
     */
    public function update(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->only(['title', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'location', 'color', 'type']));
        return $this->success($event, 'Updated');
    }

    /**
     * Delete an event
     */
    public function destroy(int $id)
    {
        Event::findOrFail($id)->delete();
        return $this->deleted('Deleted');
    }
}
