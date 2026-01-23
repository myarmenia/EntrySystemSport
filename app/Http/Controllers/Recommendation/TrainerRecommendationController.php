<?php

namespace App\Http\Controllers\Recommendation;

use App\DTO\RecommendationDto;
use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Services\Recommendation\TrainerRecommendationService;
use Illuminate\Http\Request;

class TrainerRecommendationController extends Controller
{
    public function __construct(
        private  TrainerRecommendationService $service
    ){}
    public function index(){

        $data = Recommendation::where('trainer_id',auth()->id())->get();

        return view('trainer-recommendation.index', compact('data'));
    }
    public function create(){
        return view('trainer-recommendation.create');
    }
    public function store(Request $request): void
    {
        
        $data = $this->service->store(RecommendationDto::fromRequestDto($request));
    }
}
