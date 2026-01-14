<?php

namespace App\Http\Controllers;

use App\Services\AttendanceSheetTimeService;
use Illuminate\Http\Request;

class AttendansSheetEnterTimeController extends Controller
{
    public function __construct(protected AttendanceSheetTimeService $service){}

    public function __invoke($tb_name, $person_id, $client_id, $direction, $date, $day, $enterExitTime, $time){
        // dd($tb_name, $person_id, $client_id, $direction, $date, $day, $enterExitTime, $time);
        $data = $this->service->store($tb_name, $person_id, $client_id, $direction, $date, $day, $enterExitTime, $time);
        return response()->json(['result'=>$data]);
    }
}
