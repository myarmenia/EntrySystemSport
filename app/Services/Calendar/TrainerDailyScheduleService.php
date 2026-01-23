<?php
namespace App\Services\Calendar;

use App\Repositories\TrainerDailyScheduleRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TrainerDailyScheduleService
{
    public function __construct(protected TrainerDailyScheduleRepository $repository){}

    public function index(int $trainer_id): Collection{

        $data = $this->repository->index($trainer_id);

        return $data;

    }

}
