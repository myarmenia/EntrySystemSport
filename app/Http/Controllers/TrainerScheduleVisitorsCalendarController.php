<?php

namespace App\Http\Controllers;
use App\Services\TrainerScheduleVisitorsCalendarService;


class TrainerScheduleVisitorsCalendarController extends Controller
{
    public function __construct(protected TrainerScheduleVisitorsCalendarService $service){}
    public function __invoke($schedule_id)
    {
        $data = $this->service->getTrainerScheduleVisitors($schedule_id,auth()->id());
   
        return view('calendar.trainer_schedule.index',compact('data'));
    }



}
