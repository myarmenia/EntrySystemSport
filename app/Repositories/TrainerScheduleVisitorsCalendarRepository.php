<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TrainerScheduleVisitorsCalendarRepository
{

    public function getTrainerVisitors(int $schedule_id)
    {

        $schedule_department_person = DB::table('schedule_department_people')->where('schedule_name_id',$schedule_id)->get();
        

        // $turnstile = Turnstile::where('mac', $mac)->first();
        // return $turnstile != null ? $turnstile->client_id : false;
    }
}
