<?php

namespace App\Services;
use App\Repositories\TrainerScheduleVisitorsCalendarRepository;
use Illuminate\Support\Collection;

class TrainerScheduleVisitorsCalendarService
{
    public function __construct(protected TrainerScheduleVisitorsCalendarRepository $trainerScheduleVisitor){}
    public function getTrainerScheduleVisitors(int $schedule_id,int $trainer_id): Collection
    {

        return $this->trainerScheduleVisitor->getTrainerVisitors( $schedule_id,  $trainer_id);
    }
}
