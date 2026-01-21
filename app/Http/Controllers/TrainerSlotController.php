<?php

namespace App\Http\Controllers;

use App\Models\ScheduleDetail;
use App\Models\SessionDuration;
use App\Models\PersonSessionBooking;
use App\Models\ScheduleDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrainerSlotController extends Controller
{
public function availableSlots(Request $request, int $trainer, int $scheduleName)
{
    $request->validate([
        'date' => ['required', 'date'],
        'session_duration_id' => ['required', 'exists:session_durations,id'],
    ]);

    $date = \Carbon\Carbon::parse($request->date);
    $weekDay = $date->format('l'); // Monday, Tuesday...

    $duration = \App\Models\SessionDuration::findOrFail($request->session_duration_id);
    $minutes = (int) $duration->minutes;

    $detail = ScheduleDetails::query()
        ->where('schedule_name_id', $scheduleName)
        ->where('week_day', $weekDay)
        ->whereNull('deleted_at')
        ->first();

    if (!$detail) {
        return response()->json(['slots' => []]);
    }

    // ✅ start/end datetime (overnight safe)
    $start = \Carbon\Carbon::parse($request->date . ' ' . $detail->day_start_time);
    $end   = \Carbon\Carbon::parse($request->date . ' ' . $detail->day_end_time);

    if ($end->lessThanOrEqualTo($start)) {
        $end->addDay(); // overnight
    }

    // ✅ break datetime (overnight safe)
    $breakStart = null;
    $breakEnd = null;

    if ($detail->break_start_time && $detail->break_end_time) {
        $breakStart = \Carbon\Carbon::parse($request->date . ' ' . $detail->break_start_time);
        $breakEnd   = \Carbon\Carbon::parse($request->date . ' ' . $detail->break_end_time);

        // break-ը եթե սկսում է կեսգիշերից հետո՝ տեղափոխենք հաջորդ օր
        if ($breakStart->lessThan($start)) {
            $breakStart->addDay();
        }
        if ($breakEnd->lessThanOrEqualTo($breakStart)) {
            $breakEnd->addDay();
        }
    }

    $slots = [];
    $cursor = $start->copy();

    while ($cursor->copy()->addMinutes($minutes)->lessThanOrEqualTo($end)) {
        $slotStart = $cursor->copy();
        $slotEnd   = $cursor->copy()->addMinutes($minutes);

        // ✅ break overlap check
        $inBreak = false;
        if ($breakStart && $breakEnd) {
            if ($slotStart->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                $inBreak = true;
            }
        }

        if (!$inBreak) {
            $slots[] = [
                'start' => $slotStart->format('H:i'),
                'end'   => $slotEnd->format('H:i'),
                'label' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
            ];
        }

        $cursor->addMinutes($minutes);
    }

    return response()->json(['slots' => $slots]);
}

}
