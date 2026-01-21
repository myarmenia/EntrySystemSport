<?php

namespace App\Http\Controllers;

use App\Models\PersonSessionBooking;
use App\Services\TrainerScheduleVisitorsCalendarService;
use Illuminate\Http\Request;

class GetTrainerScheduleVisitorsController extends Controller
{

    public function __construct(protected TrainerScheduleVisitorsCalendarService $service){}
    public function __invoke($schedule_id,$date)
    {

        $data = $this->service->getTrainerScheduleVisitors($schedule_id,auth()->id());

        $day = date('l', strtotime('2026-01-21'));
        $person_session_bookings = PersonSessionBooking::where(['day'=>$day,'schedule_name_id'=>$schedule_id])
        ->whereIn('person_id',$data->pluck('id'))
        ->get();
      


        return view('components.schedule', [
                                                  'reservetions' =>$person_session_bookings,
                                                //   'person_full_name' => $person->full_name,
                                                //   'url' => $url
                                                ]
                                            );


    }

}
