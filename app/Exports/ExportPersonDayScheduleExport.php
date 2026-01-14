<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;


class ExportPersonDayScheduleExport implements  FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $attendance_sheet;
    protected $date;
    protected $personFullName;

    public function __construct($attendance_sheet, $date, $personFullName)
    {
        $this->attendance_sheet = $attendance_sheet;
        $this->date = $date;
        $this->personFullName = $personFullName;
    }

    public function view(): View
    {
        return view('export.exportPersonDaySchedule', [
            'attendance_sheet' => $this->attendance_sheet,
            'yearMonthDate' => $this->date,
            'personFullName' => $this->personFullName
        ]);
    }
}
