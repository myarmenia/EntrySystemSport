<?php

namespace App\Http\Controllers\Recommendation;

use App\DTO\RecommendationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerRecommendatinRequest;
use App\Models\Person;
use App\Models\PersonSessionBooking;
use App\Models\Recommendation;
use App\Services\Recommendation\TrainerRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TrainerRecommendationController extends Controller
{
    public function __construct(
        private  TrainerRecommendationService $service
    ) {}
    public function index()
    {

        $data = Recommendation::where('trainer_id', auth()->id())->get();
        $booking = Person::where('trainer_id',auth()->id())->get();


        $i = 0;

        return view('trainer-recommendation.index', compact(['data','booking', 'i']));
    }
    public function create()
    {
        return view('trainer-recommendation.create');
    }
    public function store(TrainerRecommendatinRequest $request): RedirectResponse
    {
        try {
            $this->service->store(
                RecommendationDto::fromRequestDto($request)
            );
            return redirect()
                ->route('recommendation.list')
                ->with('success', 'Գործողությունը բարեհաջող կատարված է');
        } catch (ValidationException $e) {

            throw $e;
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Սխալ է տեղի ունեցել, փորձեք կրկին');
        }
    }
    public function edit(int $id): View
    {
        $data = $this->service->edit($id);

        return view('trainer-recommendation.edit', compact('data'));
    }
    public function update(TrainerRecommendatinRequest $request, $id): RedirectResponse
    {
        try {

            $data = $this->service->update(RecommendationDto::fromRequestDto($request), $id);

            return redirect()
                ->route('recommendation.list')
                ->with('success', 'Գործողությունը բարեհաջող կատարված է');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Սխալ է տեղի ունեցել, փորձեք կրկին');
        }
    }
}
