<?php
namespace App\Services;

use App\Repositories\Interfaces\ScheduleDetailsInterface;

class  ScheduleDetailsService
{
    protected $scheduleDetailsRepository;
    public function __construct(ScheduleDetailsInterface $scheduleDetailsRepository){

        $this->scheduleDetailsRepository = $scheduleDetailsRepository;
    }

    public function update($dto,$id){
        // dd($dto);

        $data = $this->scheduleDetailsRepository->update($dto,$id);
        return true;


    }

}
