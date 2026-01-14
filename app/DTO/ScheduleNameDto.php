<?php

namespace App\DTO;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ScheduleNameDto
{
    public function __construct(
        public string $name,

        public ?int $status = null, // Должно быть int, так как в БД integer

    )
    {}

    public static  function fromRequestDto(Request $request): ScheduleNameDto {
        return  new self(
            name: $request->name,

            status: $request->has('status') ? 1 : null // Если статус не пришёл, оставляем null

        );

    }

    public function toArray()
    {
        return array_filter([
            'name' => $this->name,
            'status' => $this->status,
        ], fn($value) => !is_null($value)); // Убираем null-значения
    }

}
