<?php

namespace App\Http\Controllers\WorkTimeManagment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkTimeManagmentController extends Controller
{
    public function index(){

        return view('work-time-managment.index');
    }
    public function create(){

        return view('work-time-managment.create');
    }
}
