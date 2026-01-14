<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryExitSystemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "id" => $this->id,
            "date" => $this->date,
            "local_ip" => $this->local_ip,
            "type" => $this->type,
            "people_id" => $this->people_id
        ];
    }
}
