<?php

namespace App\Http\Controllers;

use App\Exports\ExportPersonDayScheduleExport;
use App\Models\AttendanceSheet;
use App\Models\Person;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExpertPersonDayScheduleController extends Controller
{
    public function __invoke($date,$personId){

        $attendance_sheet = AttendanceSheet::forPersonOnDate($personId, $date);
        $person = Person::where('id',$personId)->first();
        $personFullName = $person->full_name;

        return Excel::download(new ExportPersonDayScheduleExport($attendance_sheet, $date, $personFullName), 'Հաշվետվություն-օրեկան.xlsx');

    }

}
