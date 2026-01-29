<?php

namespace App\Repositories\Reccomendation;

use App\Models\Recommendation;

class PersonRecommendationRepository
{

    public function store(array $data)
    {

        $recommendation = Recommendation::findOrFail($data['recommendation_id']);

        $recommendation->persons()->sync($data['user_ids']);
    }



}
