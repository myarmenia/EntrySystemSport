<?php

namespace App\Http\Controllers\Recommendation;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonReccomendationRequest;
use App\Http\Requests\PersonRecommendationRequest;
use App\Services\Recommendation\PersonRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonRecommendationController extends Controller
{
    public function __construct(private PersonRecommendationService $service){}


    public function store(PersonRecommendationRequest $request): JsonResponse{


        $this->service->attachPersons($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Գործողությունը բարեհաջող կատարված է',
        ]);

    }
}
