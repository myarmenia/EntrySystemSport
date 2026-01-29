<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Services\Calendar\GetTrainerVisitorsCalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class GetTrainerVisitorsCalendarController extends Controller
{
    public function __construct(
        private GetTrainerVisitorsCalendarService $service
    ){}

    public function __invoke(Request $request): JsonResponse
     {

        try {
            $events = $this->service->getEvents(

                $request->id,
                $request->start,
                $request->end
            );


            return response()->json($events);
        } catch (Throwable $e) {

            report($e); // логируем

            return response()->json([
                'message' => 'error generating calendar'
            ], 500);
        }
    }
}
