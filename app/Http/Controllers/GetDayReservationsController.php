<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSheet;
use App\Models\Person;
use DateTime;
use Illuminate\Http\Request;

class GetDayReservationsController extends Controller
{
    public function __invoke($people_id,$data){


        $reservetions= AttendanceSheet::where('people_id',$people_id)->whereDate('date',$data)->get();

        if ($reservetions) {
            $person = Person::where('id',$people_id)->first();

            $url = route('export-person-day-schedule', [
                'date' => $data,
                'personId' => $people_id       
            ]);


            return view('components.offcanvas', [
                                                  'reservetions' => $reservetions,
                                                  'person_full_name' => $person->full_name,
                                                  'url' => $url
                                                ]
                                            );
          }


    }
}
