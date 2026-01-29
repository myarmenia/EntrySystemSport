<?php

namespace App\Http\Controllers;

use App\Http\Resources\GetCalendarResource;
use App\Models\AttendanceSheet;
use App\Models\Person;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __invoke($id){

        $data = Person::where('id',$id)->first();

        return view('calendar.index',compact('data'));
    }
}
