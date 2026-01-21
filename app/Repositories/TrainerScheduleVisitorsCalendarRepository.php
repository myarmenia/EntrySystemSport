<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class TrainerScheduleVisitorsCalendarRepository
{
    public function getTrainerVisitors(int $scheduleId, int  $userId): Collection
    {
        $clientId = $this->getClientIdByUser($userId);
        $personIds = $this->getPersonIdsBySchedule($scheduleId, $clientId);

        return $this->getPeopleByIds($personIds);
    }
    protected function getClientIdByUser(int $userId): int
    {
        $clientId = DB::table('staff')
            ->where('user_id', $userId)
            ->value('client_admin_id');

        if (!$clientId) {
            throw new ModelNotFoundException('Client not found');
        }

        return $clientId;
    }
    protected function getPersonIdsBySchedule(int $scheduleId, int $clientId): array
    {

        return DB::table('schedule_department_people')
            ->where('schedule_name_id', $scheduleId)
            ->where('client_id', $clientId)
            ->pluck('person_id')
            ->toArray();
    }
    protected function getPeopleByIds(array $personIds): Collection
    {
        if (empty($personIds)) {
            return collect();
        }

        return DB::table('people')
            ->whereIn('id', $personIds)
            ->get();
    }
}
