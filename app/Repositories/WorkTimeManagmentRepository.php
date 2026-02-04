<?php

namespace App\Repositories;

use App\Interfaces\WorkTimeManagmentInterface;
use App\Models\ScheduleName;
use App\Models\ClientSchedule;
use App\Models\ScheduleDetail;
use App\Models\ClientScheduleSmoke;
use App\Models\ScheduleDetails;

class WorkTimeManagmentRepository implements WorkTimeManagmentInterface
{
    public function createScheduleName(string $name): ScheduleName
    {
        return ScheduleName::create(['name' => $name]);
    }

    public function attachClient(int $clientId, int $scheduleNameId): void
    {
        ClientSchedule::create([
            'client_id' => $clientId,
            'schedule_name_id' => $scheduleNameId,
        ]);
    }

    public function createScheduleDetail(int $scheduleNameId, array $day): void
    {
        ScheduleDetails::create([
            'schedule_name_id' => $scheduleNameId,
            'week_day' => $day['week_day'],
            'day_start_time' => $day['day_start_time'] ?? null,
            'day_end_time' => $day['day_end_time'] ?? null,
            'break_start_time' => $day['break_start_time'] ?? null,
            'break_end_time' => $day['break_end_time'] ?? null,
        ]);
    }

    public function createSmokeBreak(
        int $clientId,
        int $scheduleNameId,
        array $smoke
    ): void {
        ClientScheduleSmoke::create([
            'client_id' => $clientId,
            'schedule_name_id' => $scheduleNameId,
            'smoke_start_time' => $smoke['smoke_start_time'] ?? null,
            'smoke_end_time' => $smoke['smoke_end_time'] ?? null,
        ]);
    }
}
