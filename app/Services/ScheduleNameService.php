<?php

namespace App\Services;


use App\DTO\PersonDTO;
use App\Models\Client;
use App\Models\EntryCode;
use App\Repositories\Interfaces\PersonRepositoryInterface;
use App\Repositories\Interfaces\ScheduleNameInterface;
use Illuminate\Support\Facades\Auth;

class ScheduleNameService
{
    protected $scheduleNameRepository;


    public function __construct(ScheduleNameInterface $scheduleNameRepository)
    {
        $this->scheduleNameRepository = $scheduleNameRepository;
    }
    public function list(){

        $data = $this->scheduleNameRepository->index();
        return $data;

    }

    public function storeScheduleName($dto)
    {
// dd($dto);
// dd($dto->toArray());
        $data = $this->scheduleNameRepository->store($dto->toArray());


        return $data;
    }
    public function editScheduleName($id){
        // dd($id);

        $data = $this->scheduleNameRepository->edit($id);
        return $data;

    }
    public function updateScheduleName($dto,$id){


        if($dto->status==null){
            $dto->status=0;
        }
        $data = $this->scheduleNameRepository->update($dto->toArray(),$id);
        return true;

    }
}
