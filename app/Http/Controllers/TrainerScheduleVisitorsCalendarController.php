<?php

namespace App\Http\Controllers;

use App\Services\TrainerScheduleVisitorsCalendarService;
use Illuminate\Http\Request;

class TrainerScheduleVisitorsCalendarController extends Controller
{
    public function __construct(protected TrainerScheduleVisitorsCalendarService $service)
    {}
    public function __invoke($schedule_id)
    {
        $data = $this->service->getTrainerScheduleVisitors($schedule_id);
         dd($schedule_id);
    }



}
