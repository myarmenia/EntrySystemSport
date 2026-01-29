<?php

namespace App\DTO;

class PersonDTO
{
    public $id;
    public $entry_code_id;

    public $client_id;
    public $name;
    public $surname;
    public $schedule_name_id;
    public $department_id;
    public $email;
    public $phone;
    public $type;
    public $image;
    public $package_id;
    public $trainer_id;
    public $session_duration_id;
    public $change_package;
    public function __construct($id, $entry_code_id, $client_id, $name, $surname, $schedule_name_id, $department_id, $email, $phone, $type, $image, $package_id, $trainer_id, $session_duration_id, $change_package)
    {
        $this->id = $id;
        $this->entry_code_id = $entry_code_id;
        $this->client_id = $client_id;
        $this->name = $name;
        $this->surname = $surname;
        $this->schedule_name_id = $schedule_name_id;
        $this->department_id = $department_id;
        $this->email = $email;
        $this->phone = $phone;
        $this->type = $type;
        $this->image = $image;
        $this->package_id = $package_id;
        $this->trainer_id = $trainer_id;
        $this->session_duration_id = $session_duration_id;
        $this->change_package = $change_package;
    }

    public static function fromModel($person)
    {
        // dd($person);
        return new self(
            $person->id,
            $person->entry_code_id,
            $person->client_id,
            $person->name,
            $person->surname,
            $person->schedule_name_id,
            $person->department_id,
            $person->email,
            $person->phone,
            $person->type,
            $person->image,
            $person->package_id,
            $person->trainer_id,
            $person->session_duration_id,
            $person->change_package
        );
    }
}
