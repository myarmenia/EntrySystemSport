<?php

namespace App\Http\Controllers\Recommendation;

use App\Http\Controllers\Controller;
use App\Services\Recommendation\PersonRecommendationService;
use Illuminate\Http\Request;

class PersonRecommendationController extends Controller
{
    public function __construct(private PersonRecommendationService $service){}


    public function store(Request $request){

        $data = $this->service->store($request->all());

    }
}
