<?php

namespace App\DTO;
use Illuminate\Http\Request;
use Carbon\Carbon;
class DepartmentDto
{
    public function __construct(

        public string $name,
    )
    {}

    public static  function fromRequestDto(Request $request): DepartmentDto {
        return  new self(
            name: $request->name
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name

        ];
    }

}
