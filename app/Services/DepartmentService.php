<?php

namespace App\Services;

use App\Interfaces\DepartmentInterface;

class DepartmentService
{
    protected $departmentInterface;

    public function __construct( DepartmentInterface $departmentInterface){

        $this->departmentInterface = $departmentInterface;

    }
    public function list(){

        return $this->departmentInterface->index();
    }

    public function store($dto){

        $data = $this->departmentInterface->store($dto->toArray());
        return true;

    }
    public function edit($id){

        $data = $this->departmentInterface->edit($id);
        return $data;

    }
    public function update($dto, $id){

        $data = $this->departmentInterface->update($dto->toArray(),$id);
        return $data;
    }






}
