<?php

namespace App\Http\Controllers;

use App\Services\TrainerScheduleVisitorsCalendarService;
use Illuminate\Http\Request;

class GetTrainerScheduleVisitorsController extends Controller
{

    public function __construct(protected TrainerScheduleVisitorsCalendarService $service){}
    public function __invoke($schedule_id)
    {
        $data = $this->service->getTrainerScheduleVisitors($schedule_id,auth()->id());

        return view('components.offcanvas', [
                                                  'reservetions' =>$data,
                                                //   'person_full_name' => $person->full_name,
                                                //   'url' => $url
                                                ]
                                            );


    }

}
