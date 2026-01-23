<?php
namespace App\Services\Recommendation;

use App\Repositories\Reccomendation\TrainerRecommendationRepository;
class TrainerRecommendationService
{

     public function __construct(protected  TrainerRecommendationRepository $repository ){}

     public function store($data){

        $data=$this->repository->store($data->toArray());
     }


}
