<?php

namespace App\DTO;

class WorkTimeManagmentDto
{
    public function __construct(
        public string $name,
        public ?int $status,
        public array $weekDays
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            status: array_key_exists('status', $data) ? 1 : 0,
            weekDays: $data['week_days']
        );
    }
}
