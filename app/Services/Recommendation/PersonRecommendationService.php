<?php

namespace App\Services\Recommendation;

use App\Repositories\Reccomendation\PersonRecommendationRepository;

class PersonRecommendationService
{

    public function __construct(protected  PersonRecommendationRepository  $repository) {}

    public function store($data)
    {

        return $this->repository->store($data);
    }


}
