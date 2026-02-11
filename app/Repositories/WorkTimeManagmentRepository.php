<?php

namespace App\Repositories;

use App\Helpers\MyHelper;
use App\Interfaces\WorkTimeManagmentInterface;
use App\Models\ScheduleName;
use App\Models\ClientSchedule;
use App\Models\ScheduleDetail;
use App\Models\ClientScheduleSmoke;
use App\Models\ScheduleDetails;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkTimeManagmentRepository implements WorkTimeManagmentInterface
{
     public function index(): Collection
    {
        $client_schedules = ClientSchedule::where('client_id', MyHelper::find_auth_user_client())->pluck('schedule_name_id');
        // dd($client_schedules);
        if (auth()->user()->hasRole('trainer')) {
            $schedule_name_user = DB::table('schedule_name_user')
                                ->where('user_id', auth()->id())
                                ->pluck('schedule_name_id');

            $data = ScheduleName::whereIn('id', $schedule_name_user)->with(['schedule_details','client_schedule_smokes'])->latest()->get();
        } else {
            $data = ScheduleName::whereIn('id', $client_schedules)->with(['schedule_details','client_schedule_smokes'])->latest()->get();
        }

        return $data;
    }


    public function createScheduleName(string $name, int $status ): ScheduleName
    {

        return ScheduleName::create(['name' => $name,'status' => $status ]);
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
        string $day,
        array $smoke
    ): void {
        ClientScheduleSmoke::create([
            'client_id' => $clientId,
            'schedule_name_id' => $scheduleNameId,
            'week_day' => $day,
            'smoke_start_time' => $smoke['smoke_start_time'] ?? null,
            'smoke_end_time' => $smoke['smoke_end_time'] ?? null,
        ]);
    }

     public function edit($id): ScheduleName
    {

        $data = ScheduleName::with('schedule_details','client_schedule_smokes')->findOrFail($id);

        return $data;
    }
}
