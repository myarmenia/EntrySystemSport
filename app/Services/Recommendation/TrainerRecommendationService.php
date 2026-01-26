<?php

namespace App\Services\Recommendation;

use App\DTO\RecommendationDto;
use App\Models\Recommendation;
use App\Repositories\Reccomendation\TrainerRecommendationRepository;

class TrainerRecommendationService
{

    public function __construct(protected  TrainerRecommendationRepository $repository) {}

    public function store($data): Recommendation
    {

        return $this->repository->store($data->toArray());
    }

    public function edit(int $id): Recommendation
    {
        $data = $this->repository->edit($id);
        return $data;
    }
    public function update(RecommendationDto $dto, int $id): Recommendation
    {
        $data = $this->repository->update($dto->toArray(), $id);
        return $data;
    }
}
