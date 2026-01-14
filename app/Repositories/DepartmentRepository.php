<?php

namespace App\Repositories;

use App\Helpers\MyHelper;
use App\Interfaces\DepartmentInterface;
use App\Models\Client;
use App\Models\Department;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;

class DepartmentRepository implements DepartmentInterface
{

    public function index(){

        return Department::where('client_id',MyHelper::find_auth_user_client ())
                           ->get();
    }

    public function store($dto){

        if(auth()->user()->hasAnyRole(['client_admin','client_admin_rfID','client_sport'])){

            $client_id = Client::where('user_id',Auth::id())->value('id');
        }else{

            $client_id = Staff::where('user_id',Auth::id())->value('client_admin_id');
        }

        $dto['client_id'] = $client_id;

        $data = Department::create($dto);

        return true;
    }
    public function edit($id){

        return Department::find($id);

    }
    public function update($dto,$id){

// dd($dto);
        $data = Department::find($id);
        if ($data) {
          $data->update($dto);

        }

        return true;

    }
}
