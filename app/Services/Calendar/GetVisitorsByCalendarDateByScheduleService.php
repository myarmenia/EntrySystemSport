<?php
namespace App\Services\Calendar;

use App\Models\PersonSessionBooking;
use Carbon\Carbon;
use Illuminate\Support\Collection;
class GetVisitorsByCalendarDateByScheduleService
{
public function getEvents(
        int $scheduleId,
        int $trainerId,
        string $start,
        string $end
    ): Collection
    {
        $from = Carbon::parse($start);
        $to   = Carbon::parse($end);

        $bookings = PersonSessionBooking::where('schedule_name_id', $scheduleId)
            ->where('trainer_id', $trainerId)
            ->get();

        return $this->generateEvents($bookings, $from, $to);
    }
    private function generateEvents($bookings, Carbon $from, Carbon $to): Collection
    {
        $events = collect();

        foreach ($bookings as $booking) {
            $current = $from->copy();

            while ($current <= $to) {
                if ($current->englishDayOfWeek === $booking->day) {
                    $events->push([
                        'start' => $current->format('Y-m-d') . ' ' . $booking->start_time,
                        'end'   => $current->format('Y-m-d') . ' ' . $booking->end_time,
                    ]);
                }

                $current->addDay();
            }
        }

        return $events;
    }

}
