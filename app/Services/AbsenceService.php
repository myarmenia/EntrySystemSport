<?php
namespace App\Services;

use App\Interfaces\AbsenceInterface;
use Maatwebsite\Excel\Concerns\ToArray;

class AbsenceService
{
    protected $absenceRepository;
    public function __construct(AbsenceInterface $absenceRepository ){

        $this->absenceRepository = $absenceRepository;

    }

    public function list($person){
        // dd($person);

        return $this->absenceRepository->index($person);
    }
    public function store($dto){


        return $this->absenceRepository->store($dto->toArray());

    }
    public function edit($id){

        return $this->absenceRepository->edit($id);
    }
    public function update($dto,$id){

        return $this->absenceRepository->update($dto->ToArray(),$id);
    }


}
