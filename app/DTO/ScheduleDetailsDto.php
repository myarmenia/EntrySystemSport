<?php

namespace App\DTO;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ScheduleDetailsDto
{
    public function __construct(
        public int $schedule_name_id,
        public string $week_day,
        public string $day_start_time,
        public string $day_end_time,
        public ?string $break_start_time,
        public ?string $break_end_time,


    )
    {}

    public static  function fromRequestDto(Request $data): array {

        return array_map(fn($weekDay) => new self(
            schedule_name_id: $weekDay['schedule_name_id'],
            week_day: $weekDay['week_day'],
            day_start_time: $weekDay['day_start_time'],
            day_end_time: $weekDay['day_end_time'],
            break_start_time: $weekDay['break_start_time'],
            break_end_time: $weekDay['break_end_time']
        ), $data['week_days']);
    }

    public function toArray()
    {
        return [
            'schedule_name_id' => $this->schedule_name_id,
            'week_day' => $this->week_day,
            'day_start_time' =>Carbon::parse($this->day_start_time)->format('H:i') ,
            'day_end_time' =>Carbon::parse($this->day_end_time)->format('H:i'),
            'break_start_time' =>Carbon::parse($this->break_start_time),
            'break_end_time' =>Carbon::parse($this->break_end_time)->format('H:i'),

        ];
    }

}
