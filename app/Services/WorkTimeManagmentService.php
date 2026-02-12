<?php

namespace App\Services;

use App\DTO\WorkTimeManagmentDto;
use App\Interfaces\WorkTimeManagmentInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkTimeManagmentService
{
    public function __construct(
        protected WorkTimeManagmentInterface $repository
    ) {}
    public function list(): Collection
    {

        $data = $this->repository->index();
        return $data;
    }
    public function store(
        WorkTimeManagmentDto $dto,
        int $clientId
    ): void {
        DB::transaction(function () use ($dto, $clientId) {

            $schedule = $this->repository
                ->createScheduleName($dto->name, $dto->status);

            $this->repository
                ->attachClient($clientId, $schedule->id);

            foreach ($dto->weekDays as $day) {


                if (!empty($day['day_start_time']) && !empty($day['day_end_time'])) {
                    $this->repository
                        ->createScheduleDetail($schedule->id, $day);
                }

                if (!empty($day['smoke_break'])) {
                    foreach ($day['smoke_break'] as $smoke) {
                        $this->repository
                            ->createSmokeBreak(
                                $clientId,
                                $schedule->id,
                                $day['week_day'],
                                $smoke
                            );
                    }
                }
            }
        });
    }
    public function editScheduleName($id){
        // dd($id);

        $data = $this->repository->edit($id);
        return $data;

    }
    public function update(
        int $scheduleId,
        WorkTimeManagmentDto $dto,
        int $clientId
    ): void {
        DB::transaction(function () use ($scheduleId, $dto, $clientId) {

            // update schedule name
            $this->repository->updateScheduleName(
                $scheduleId,
                $dto->name,
                $dto->status
            );

            // deleting old data
            $this->repository->deleteScheduleDetails($scheduleId);
            $this->repository->deleteSmokeBreaks($scheduleId, $clientId);

            // creating new data by request 
            foreach ($dto->weekDays as $day) {

                if (!empty($day['day_start_time']) && !empty($day['day_end_time'])) {
                    $this->repository
                        ->createScheduleDetail($scheduleId, $day);
                }

                if (!empty($day['smoke_break'])) {
                    foreach ($day['smoke_break'] as $smoke) {
                        $this->repository->createSmokeBreak(
                            $clientId,
                            $scheduleId,
                            $day['week_day'],
                            $smoke
                        );
                    }
                }
            }
        });
    }

}
