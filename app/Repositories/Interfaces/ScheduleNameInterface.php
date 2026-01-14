<?php
namespace App\Repositories\Interfaces;

interface  ScheduleNameInterface
{
    public function index();
    public function creat();
    public function store($dto);
    public function  edit($id);
    public function update($dto,$id);
}
