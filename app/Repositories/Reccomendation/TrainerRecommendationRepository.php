<?php

namespace App\Repositories\Reccomendation;
use Illuminate\Support\Facades\DB;
class TrainerRecommendationRepository
{
    public function store($dto): void
    {
        $data = DB::table('recommendations')->insert($dto);
    }
}
