<?php

namespace App\Repositories\Interfaces;

use App\DTO\PersonDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PersonRepositoryInterface
{
    public function getAllPeople(?string $type = null): LengthAwarePaginator;
    public function createPerson();
    public function storePerson(PersonDTO $personDTO);
    public function editPerson($personId);
    public function updatePerson(PersonDTO $personDTO, array $data);
}
