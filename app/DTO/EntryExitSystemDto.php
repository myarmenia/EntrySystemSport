<?php

namespace App\DTO;
use Illuminate\Http\Request;

class EntryExitSystemDto
{

    public function __construct(
        public readonly string $mac,
        public string $entry_code,
        public string $direction,
        public readonly string $local_ip,
        public readonly string $type,
        public ?int $people_id = null,
        public ?bool $online = null,
        public ?string $date = null,


    ) {
    }

    public static function fromEntryExitSystemDto(Request $request): EntryExitSystemDto
    {
        return new self(
            mac: $request->mac,
            entry_code: $request->entry_code,
            direction: $request->direction,
            local_ip: $request->local_ip,
            type: $request->type,
            online: $request->online,
        );
    }

    public function toArray()
    {
        return [
            'direction' => $this->direction,
            'local_ip' => $this->local_ip,
            'entry_code' => $this->entry_code,
            'type' => $this->type,
            'people_id' => $this->people_id,
            'online' => $this->online,
            'date' => $this->date,
            'mac' => $this->mac

        ];
    }


}
