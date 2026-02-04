<?php

namespace App\Http\Controllers\WorkTimeManagment;

use App\Helpers\MyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTimeManagmentRequest;
use Illuminate\Http\Request;


class WorkTimeManagmentController extends Controller
{
    public function index(){

        return view('work-time-managment.index');
    }
    public function create(){

        $weekdays =MyHelper::week_days();

        return view('work-time-managment.create',compact('weekdays'));
    }
    public function store(WorkTimeManagmentRequest $request){
        dd($request->all());
    }
}
