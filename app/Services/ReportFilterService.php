<?php
namespace App\Services;

use App\Helpers\MyHelper;
use App\Models\Absence;
use App\Models\AttendanceSheet;
use App\Traits\ReportFilterTrait;
use Carbon\Carbon;

class ReportFilterService

{
    use ReportFilterTrait;

    public function __construct(protected AttendanceSheet $model){}

    public function filterService($data){

        $data['month'] = $data['month'] ??\Carbon\Carbon::now()->format('Y-m');

        $department_id = $data['department_id'] ?? null;


        session()->put('selected_month',  $data['month']);
         // dd($data['month']);
        $attendance_sheet = AttendanceSheet::forClient($data['month'], $department_id)->get();
        // dd($attendance_sheet);
        $data['attendance_sheet'] = $attendance_sheet;
        // dd($data['attendance_sheet']);
        $data['client_department'] = Myhelper::get_client_department();
        $data['client_id'] = Myhelper::find_auth_user_client();

        $service = $this->filter($data);
        // dd($service);

        $person_data_from_attendance = $this->fill_absent_person_date($service,$data['month']);

        $data['attendance_sheet']= $person_data_from_attendance;
        $data['i']=0;

// dd($data);
        return $data;

    }
    public function fill_absent_person_date($service,$month){


        foreach($service as $peopleId => &$dailyRecords){
            // dd($peopleId,  $dailyRecords);
            // if($peopleId==62){
                 $absence = Absence::where('person_id',$peopleId)->get();

                if(count($absence)>0){

                }
                foreach($absence as $key=>$ab){
                    // if($key==1){
                        $currentMonth = Carbon::parse($month . '-01');
                        $startOfMonth = $currentMonth->copy()->startOfMonth();
                        $endOfMonth = $currentMonth->copy()->endOfMonth();

                        $periodStart = Carbon::parse($ab->start_date); // например, 2025-05-29
                        $periodEnd = Carbon::parse($ab->end_date);     // например, 2025-06-06

                        // Рассчитываем пересечение диапазонов
                        $startDate = $periodStart->greaterThan($startOfMonth) ? $periodStart : $startOfMonth;
                        $endDate = $periodEnd->lessThan($endOfMonth) ? $periodEnd : $endOfMonth;
                        // dd($startOfMonth,$startDate, $endDate);

                        //  Если диапазон не валиден, значит ничего не попадает в месяц
                            if ($startDate->gt($endDate)) {
                                continue;
                            }

                            // Заполняем дни
                            for ($day = $startDate->copy(); $day->lte($endDate); $day->addDay()) {
                                $dayKey = $day->format('d'); // 29, 30, 31 и т.д.
                                $dailyRecords[$dayKey]['absence'] = $ab->type;
                            }

                    // }   //if($key==1){


                }

            // } //if($peopleId==62){



        }

        return $service;

    }


    public function getAllMonthsReport($year_month){
// dd($year);
        // $lastMonth =  \Carbon\Carbon::now()->format('Y-m');
         $lastMonth =  $year_month;
        $department_id = null;


        // Разбиваем год и месяц
        [$year, $lastMonthNum] = explode('-', $lastMonth);

        $reports = [];
        $totalWorkingTimeAllMonths = 0; // часы


    // creating cicle for all months
    for ($m = 1; $m <= (int) $lastMonthNum; $m++) {
        $monthFormatted = sprintf('%04d-%02d', $year, $m); // например 2025-01, 2025-02

        // getting data from AttendanceSheet
        $attendance_sheet = AttendanceSheet::forClient($monthFormatted, $department_id)->get();

        $monthData = [];
        $monthData['month'] = $monthFormatted;
        $monthData['attendance_sheet'] = $attendance_sheet;
        $monthData['client_department'] = MyHelper::get_client_department();
        $monthData['client_id'] = MyHelper::find_auth_user_client();

        // filtering
        $service = $this->filter($monthData);

        // Заполняем отсутствующих
        $person_data_from_attendance = $this->fill_absent_person_date($service, $monthFormatted);

        $monthData['attendance_sheet'] = $person_data_from_attendance;
        $monthData['i'] = 0;

        // Добавляем в массив по ключу месяца
        $reports[$monthFormatted] = $monthData;
    }
// dd($reports);
    return $reports;

    }



}
