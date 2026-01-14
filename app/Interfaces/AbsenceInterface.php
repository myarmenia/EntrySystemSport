<?php

namespace App\Interfaces;

interface AbsenceInterface
{
    public function index(array $person);

    public function store($dto);

    public function edit ($id);
    public function update($dto,$id);

}
