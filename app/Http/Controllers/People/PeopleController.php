<?php

namespace App\Http\Controllers\People;

use App\DTO\NewPersonDto;
use App\DTO\PersonDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use App\Models\Person;
use App\Models\PersonSessionBooking;
use App\Models\User;
use App\Services\PersonService;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    protected PersonService $personService;

    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    // --------------------------
    // LISTS
    // --------------------------

    public function indexVisitors(Request $request)
    {
        $type = 'visitor';
        $data = $this->personService->getPeopleList($type);
        return view('people.index', compact('data', 'type'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }



    //public function indexWorkers(Request $request)
    //{
    //    $type = 'worker';
    //    $data = $this->personService->getPeopleList($type);
    //
    //    return view('worker.index', compact('data', 'type'))
    //        ->with('i', ($request->input('page', 1) - 1) * 10);
    //}

    // --------------------------
    // CREATE
    // --------------------------

    // ✅ Visitor create (people.create view)
    public function create()
    {
        $entry_codes = $this->personService->create();
        if ($entry_codes === false) {
            return redirect()->back()->with('error', 'Նախ ստեղծեք հերթափոխ և ստորաբաժանում');
        }

        $packages = $this->personService->getPackagesByDiscount();

        $trainers = $this->personService->getTrainersList();
        //dd($packages->toArray());
        return view('people.create', compact('entry_codes', 'packages', 'trainers'));
    }

    public function store(PersonRequest $request)
    {

        $request->merge([
            'type' => 'visitor',
            // package_id թող մնա user-ի ընտրածը
        ]);

        $data = $this->personService->store(NewPersonDto::fromRequestDto($request));
        if ($data) {
            return redirect()->route('visitors.list')->with('success', 'Visitor created');
        }
        return back()->withErrors('Failed to create visitor');
    }

    public function edit(string $id)
    {
        $data = $this->personService->edit($id);
        $packages = $this->personService->getPackagesByDiscount();
        $trainers = $this->personService->getTrainersList(); // ✅ add
        $booking = PersonSessionBooking::where('person_id', $id)
            ->latest('id')
            ->first();
        //dd($packages->toArray());
        $bookings = PersonSessionBooking::where('person_id', $id)
            ->orderBy('day')        // optional
            ->orderBy('start_time') // optional
            ->get();

        $data['bookings'] = $bookings;

        $latestPayment = \App\Models\PersonPayment::where('person_id', $id)
            ->latest('id')
            ->first();

        $data['latest_payment'] = $latestPayment;


        if (($data['person_connected_schedule_department']['person'] ?? null) != null) {
            //dd($data);
            return view('people.edit', compact('data', 'packages', 'trainers')); // ✅ add
        }

        return redirect()->back()->withErrors('Person not found.');
    }


    public function updateVisitor(PersonRequest $request, Person $person)
    {
        $request->merge(['type' => 'visitor']);



        $person['entry_code_id'] = $request->entry_code_id;
        $personDTO = PersonDTO::fromModel($person);

        $data = $this->personService->update($personDTO, $request->all());

        if ($data) return redirect()->route('visitors.list')->with('success', 'Visitor updated');
        return back()->withErrors('Failed to update visitor');
    }

    public function destroy(string $id)
    {
        // ...
    }
}
