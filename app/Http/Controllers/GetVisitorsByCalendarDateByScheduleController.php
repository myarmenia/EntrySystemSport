<?php

namespace App\Http\Controllers;

use App\Services\Calendar\GetVisitorsByCalendarDateByScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GetVisitorsByCalendarDateByScheduleController extends Controller
{
    public function __invoke(
        Request $request,
        GetVisitorsByCalendarDateByScheduleService $service
    ): JsonResponse {
        try {
            $events = $service->getEvents(
                (int) $request->schedule_id,
                auth()->id(),
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
