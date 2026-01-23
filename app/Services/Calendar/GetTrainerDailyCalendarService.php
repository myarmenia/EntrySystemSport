<?php

namespace App\Services\Calendar;
use App\Models\PersonSessionBooking;
use Illuminate\Support\Collection;

class GetTrainerDailyCalendarService
{
    public function index(int $trainer_id, string $date):  Collection
    {
        $day = date('l', strtotime(datetime: $date));
        $person_session_bookings = PersonSessionBooking::where(['day' => $day, 'trainer_id' => $trainer_id])
            ->with('person')
            ->get();

        return $person_session_bookings;
    }
}
