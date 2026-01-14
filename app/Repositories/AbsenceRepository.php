<?php
namespace App\Repositories;

use App\Interfaces\AbsenceInterface;
use App\Models\Absence;
use Illuminate\Database\Eloquent\Collection;

class AbsenceRepository implements AbsenceInterface
{

    public function index( $person): Collection
    {

        return   Absence::where('person_id', $person->id)->latest()->get();
    }
    public function store($dto): Absence{


        return Absence::create($dto);

    }
    public function edit($id): ?Absence {

        return Absence::find($id);

    }
    public function update($dto,$id){

        $data = Absence::find($id);
        if($data){
            $data->update($dto);
        }

        return true;

    }
}



