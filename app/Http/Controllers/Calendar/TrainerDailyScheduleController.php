<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Services\Calendar\TrainerDailyScheduleService;
use Illuminate\View\View;

class TrainerDailyScheduleController extends Controller
{
    public function __construct(private TrainerDailyScheduleService $service) {}
    public function __invoke(int $id): View
    {

        $data = $this->service->index($id);


        return view('calendar.trainer-daily-schedule-calendar.index', compact('data'));
    }
}
