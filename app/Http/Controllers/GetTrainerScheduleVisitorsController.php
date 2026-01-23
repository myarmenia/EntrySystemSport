<?php

namespace App\Http\Controllers;

use App\Models\PersonSessionBooking;
use App\Services\Calendar\TrainerScheduleVisitorsCalendarService;
use Carbon\Carbon;
use Illuminate\View\View;

class GetTrainerScheduleVisitorsController extends Controller
{

    public function __construct(protected TrainerScheduleVisitorsCalendarService $service){}
    public function __invoke($schedule_id,$date): View
    {

        $data = $this->service->getTrainerScheduleVisitors($schedule_id,auth()->id());

        $day = date('l', strtotime($date));
        $person_session_bookings = PersonSessionBooking::where(['day'=>$day,'schedule_name_id'=>$schedule_id])
        ->whereIn('person_id',$data->pluck('id'))
        ->with('person')
        ->get();

        return view('components.schedule', [
                                                  'reservetions' =>$person_session_bookings,
                                                  'date' => Carbon::parse($date),

                                                ]
                                            );


    }

}
