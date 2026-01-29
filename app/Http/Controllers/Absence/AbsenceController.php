<?php

namespace App\Http\Controllers\Absence;

use App\DTO\AbsenceDto;
use App\Helpers\MyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Models\Absence;
use App\Models\AbsenceModel;
use App\Models\Person;
use App\Models\PersonSessionBooking;
use App\Services\AbsenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    public function __construct(protected AbsenceService $service) {}
    public function index(Request $request, Person $person)
    {

        $data = $this->service->list($person);
        $i = 0;
        return view('absence.index', compact('i', 'data', 'person'));
    }
    public function show(Person $person)
    {



        $absence_type = MyHelper::absence_type();
        return view('absence.create', compact('person', 'absence_type'));
    }
    //public function store(AbsenceRequest $request){
    //
    //    $data = $this->service->store(AbsenceDto::fromRequestDto($request));
    //    dd( $data);
    //    $person = Person::find($request->person_id);
    //    return redirect()->route('absence.list',$person);
    //
    //
    //}

    public function store(AbsenceRequest $request)
    {
        DB::transaction(function () use ($request) {

            $data = $this->service->store(AbsenceDto::fromRequestDto($request));

            // $data կարող է լինել array կամ model — ապահով վերցնենք արժեքները
            $start = data_get($data, 'start_date');
            $end   = data_get($data, 'end_date');
            $personId = data_get($data, 'person_id', $request->person_id);

            // ✅ Օրերի քանակ (inclusive)
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate   = Carbon::parse($end)->startOfDay();

            if ($endDate->lt($startDate)) {
                throw new \RuntimeException("end_date cannot be before start_date");
            }

            $absenceDays = $startDate->diffInDays($endDate) + 1; // inclusive

            // ✅ Բոլոր booking-ների session_end_time-ը + absenceDays
            PersonSessionBooking::where('person_id', $personId)
                ->whereNotNull('session_end_time')
                ->get()
                ->each(function ($b) use ($absenceDays) {
                    $b->session_end_time = Carbon::parse($b->session_end_time)
                        ->addDays($absenceDays)
                        ->toDateString();
                    $b->save();
                });
        });

        $person = Person::findOrFail($request->person_id);
        return redirect()->route('absence.list', $person);
    }
    public function edit($id)
    {


        $data = $this->service->edit($id);
        if ($data) {
            $absence_type = MyHelper::absence_type();
            return view('absence.edit', compact('data', 'id', "absence_type"));
        }
    }
    public function update(Request $request, $id)
    {

        $this->service->update(AbsenceDto::fromRequestDto($request), $id);
        return redirect()->back();
    }
}
