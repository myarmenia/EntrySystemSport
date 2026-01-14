<?php
namespace App\DTO;
use Illuminate\Http\Request;
 class AbsenceDto{

    public function __construct(

        public int $person_id,
        public string $type,
        public string $start_date,
        public string $end_date,

    )
    {}

    public static  function fromRequestDto(Request $request): AbsenceDto {
        return  new self(
            person_id: $request->person_id,
            type: $request->type,
            start_date: $request->start_date,
            end_date: $request->end_date,

        );
    }

    public function toArray(): array
    {
        return [
            "person_id" => $this->person_id,
            "type" => $this->type,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,

        ];
    }



 }
