<?php

namespace App\Services;

use App\DTO\UserDto;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\TrainerScheduleVisitorsCalendarRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
class TrainerScheduleVisitorsCalendarService
{
    public function __construct(protected TrainerScheduleVisitorsCalendarRepository $trainerScheduleVisitor){}
    public function getTrainerScheduleVisitors(int $schedule_id)
    {

        return $this->trainerScheduleVisitor->getTrainerVisitors( $schedule_id);
    }
}
