<?php

namespace App\DTO;
use Illuminate\Http\Request;
use Carbon\Carbon;
class RecommendationDto
{
    public function __construct(
        public string $name,
        public string $description

    )
    {}

    public static  function fromRequestDto(Request $request): self {

        return  new self(
            name: $request->name,
            description: $request->description

        );

    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

}
