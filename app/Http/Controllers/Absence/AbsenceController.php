<?php

namespace App\Http\Controllers\Absence;

use App\DTO\AbsenceDto;
use App\Helpers\MyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AbsenceRequest;
use App\Models\Absence;
use App\Models\AbsenceModel;
use App\Models\Person;
use App\Services\AbsenceService;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function __construct(protected AbsenceService $service){}
    public function index(Request $request,Person $person){

        $data = $this->service->list($person);
        $i=0;
        return view('absence.index',compact('i','data','person'));



    }
    public function show(Person $person){



        $absence_type = MyHelper::absence_type();
        return view('absence.create',compact('person','absence_type'));

    }
    public function store(AbsenceRequest $request){

        $data = $this->service->store(AbsenceDto::fromRequestDto($request));
        $person = Person::find($request->person_id);
        return redirect()->route('absence.list',$person);


    }
    public function edit($id){


        $data = $this->service->edit($id);
        if($data){
            $absence_type = MyHelper::absence_type();
            return view('absence.edit',compact('data','id', "absence_type"));
        }

    }
    public function update(Request $request,$id){

        $this->service->update(AbsenceDto::fromRequestDto($request),$id);
        return redirect()->back();
    }


}
