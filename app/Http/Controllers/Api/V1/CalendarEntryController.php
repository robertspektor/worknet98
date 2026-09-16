<?php

namespace App\Http\Controllers\Api\V1;

use App\Calendar\CalendarBook;
use App\Http\Requests\StoreCalendarEntryRequest;
use App\Http\Resources\CalendarEntryResource;
use App\Models\CalendarEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CalendarEntryController extends ApiController
{
    public function index(Request $request, CalendarBook $calendar): AnonymousResourceCollection
    {
        return CalendarEntryResource::collection($calendar->upcomingEntriesOf($this->player($request)));
    }

    public function store(StoreCalendarEntryRequest $request, CalendarBook $calendar): JsonResponse
    {
        $entry = $calendar->add($this->player($request), $request->entryDate(), $request->entryTime(), $request->entryTitle());

        return (new CalendarEntryResource($entry))->response()->setStatusCode(201);
    }

    public function destroy(CalendarEntry $calendarEntry, CalendarBook $calendar): Response
    {
        $calendar->remove($calendarEntry);

        return response()->noContent();
    }
}
