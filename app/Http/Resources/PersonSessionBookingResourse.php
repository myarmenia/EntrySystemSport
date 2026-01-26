<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonSessionBookingResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "title" =>  'start',
            "start" => "2026-01-22 15:10:00",
            "end" =>"2026-01-22 15:20:00"
            ];
    }
}
