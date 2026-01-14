<?php

namespace App\Http\Controllers;

use App\Http\Resources\GetCalendarResource;
use App\Models\AttendanceSheet;
use Illuminate\Http\Request;

class GetCalendarDataController extends Controller
{
    //
    public function __invoke($id){

        $data = AttendanceSheet::where('people_id',$id)->get();

        $result = GetCalendarResource::collection($data);
// dd($result);
        if ($result) {
          return response()->json($result);
        }
    }
}
