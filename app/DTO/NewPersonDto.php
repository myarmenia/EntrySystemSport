<?php

namespace App\DTO;

class NewPersonDto
{

    public function __construct(
        public ?int $client_id = null,
        public int $entry_code_id,
        public string $name,
        public string $surname,
        public ?int  $schedule_name_id = null,
        public ?int  $department_id = null,
        public ?string $email = null,
        public ?string $phone = null,
        public string $type,
        public ?object  $image = null,
        public ?int  $package_id = null,
        public ?int  $trainer_id = null,
        public ?int  $session_duration_id = null,
        public ?string  $session_date = null,
        public ?string $start_time = null,
        public ?string $end_time = null,
        public ?string $weekly_slots_json = null,        
    ) {}

    public static  function fromRequestDto($request): NewPersonDto
    {
        return  new self(
            client_id: $request->client_id,
            entry_code_id: $request->entry_code_id,
            name: $request->name,
            surname: $request->surname,
            schedule_name_id: $request->schedule_name_id,
            department_id: $request->department_id,
            email: $request->email,
            phone: $request->phone,
            type: $request->type,
            package_id: $request->package_id,
            trainer_id: $request->trainer_id,
            session_duration_id: $request->session_duration_id,
            session_date: $request->session_date,
            start_time: $request->start_time,
            end_time: $request->end_time,
            weekly_slots_json: $request->weekly_slots_json,
            image: $request->hasFile('image') ? $request->file('image') : null
        );
    }

    public function toArray()
    {

        return array_filter([
            "client_id" => $this->client_id,
            "entry_code_id" => $this->entry_code_id,
            "name" => $this->name,
            "surname" => $this->surname,
            "schedule_name_id" => $this->schedule_name_id,
            "department_id" => $this->department_id,
            "email" => $this->email,
            "phone" => $this->phone,
            "type" => $this->type,
            "image" => $this->image,
            "package_id" => $this->package_id,
            "trainer_id" => $this->trainer_id,
            "session_duration_id" => $this->session_duration_id,
            "session_date" => $this->session_date,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            "weekly_slots_json" => $this->weekly_slots_json

        ], fn($value) => !is_null($value)); // Убираем null-значения
    }
}
