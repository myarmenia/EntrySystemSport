<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Services\Calendar\GetTrainerDailyCalendarService;
use Carbon\Carbon;
use Illuminate\View\View;

class GetTrainerDailyCalendarController extends Controller
{
    public function __construct(protected GetTrainerDailyCalendarService $service){}
    public function __invoke($trainer_id, $date): View
    {
        $data = $this->service->index($trainer_id, $date);

        return view('components.schedule', [
                                                  'reservetions' =>$data,
                                                  'date' => Carbon::parse($date),
                                                ]
                                            );

    }

}

