<?php

namespace App\Repositories\Reccomendation;

use App\Models\Recommendation;

class TrainerRecommendationRepository
{
    public function store(array $dto): Recommendation
    {

        return Recommendation::create($dto);
    }
    public function edit(int $id): Recommendation
    {

        return Recommendation::findOrFail($id);
    }
    public function update(array $data, int $id): Recommendation
    {
        $recommendation = $this->edit($id);
        $recommendation->update($data);

        return $recommendation;
    }
}
