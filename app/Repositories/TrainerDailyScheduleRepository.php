<?php
namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrainerDailyScheduleRepository
{
    public function index(int $trainer_id): Collection
    {
        $data = DB::table('person_session_bookings')->where('trainer_id',$trainer_id)->get();
        return $data;
    }

}

