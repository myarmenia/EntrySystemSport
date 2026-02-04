<?php
namespace App\Services;

use App\DTO\WorkTimeManagmentDto;
use App\Interfaces\WorkTimeManagmentInterface;
use Illuminate\Support\Facades\DB;

class WorkTimeManagmentService
{
     public function __construct(
        protected WorkTimeManagmentInterface $repository
    ) {}
     public function store(
        WorkTimeManagmentDto $dto,
        int $clientId
    ): void {
        DB::transaction(function () use ($dto, $clientId) {

            $schedule = $this->repository
                ->createScheduleName($dto->name);

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
                                $smoke
                            );
                    }
                }
            }
        });
    }
}
