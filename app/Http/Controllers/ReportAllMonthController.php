<?php

namespace App\Http\Controllers;

use App\Services\ReportFilterService;
use Illuminate\Http\Request;

class ReportAllMonthController extends Controller
{
    public $year;
    public function __construct(protected  ReportFilterService $service){}
    public  function __invoke(Request $request){
    // dd($request->all());

     $year_month = $request['month']??  \Carbon\Carbon::now()->format('Y-m');
    session()->put('selected_month',  $year_month);
// dd($year_month);



        // Получаем массив отчётов по месяцам
    $reports =$this->service->getAllMonthsReport($year_month);
    // dd($reports);


    $armenianMonths = [
        '01' => 'Հունվար',
        '02' => 'Փետրվար',
        '03' => 'Մարտ',
        '04' => 'Ապրիլ',
        '05' => 'Մայիս',
        '06' => 'Հունիս',
        '07' => 'Հուլիս',
        '08' => 'Օգոստոս',
        '09' => 'Սեպտեմբեր',
        '10' => 'Հոկտեմբեր',
        '11' => 'Նոյեմբեր',
        '12' => 'Դեկտեմբեր',
    ];
    // $lastMonth =  \Carbon\Carbon::now()->format('Y-m');
     $lastMonth = $year_month;
    [$year, $lastMonthNum] = explode('-', $lastMonth);
    $workingMonth=[];

    for ($m = 1; $m <= (int) $lastMonthNum; $m++) {
        $key = str_pad($m, 2, '0', STR_PAD_LEFT); // добавляем ведущий ноль
        $workingMonth[$key] = $armenianMonths[$key];
    }



    // Формируем массив пользователей с суммами по месяцам
    $usersReport = [];

    foreach ($reports as $monthKey => $monthData) {
        // dd($monthKey,$monthData);
        if ($monthKey === 'totals') continue;

        $monthNumber = explode('-', $monthKey)[1];
        $monthName = $workingMonth[$monthNumber];

        foreach ($monthData['attendance_sheet'] as $personId => $personData) {
            // dd($personId, $personData['totaldelay']);

            if (!isset($usersReport[$personId])) {
                $usersReport[$personId] = [
                    'id' => $personId,
                    'totalDays' => 0,
                    'totalHours' => 0,
                    'totalDelay' => 0,
                ];
            }

            $usersReport[$personId]['months'][$monthName] = [
                'days' => $personData['totalMonthDayCount'] ?? 0,
                'hours' => $personData['totalWorkingTime'] ?? 0,
                'delay' => $personData['totaldelay'] ?? 0,
            ];

            $usersReport[$personId]['totalDays'] += $personData['totalMonthDayCount'] ?? 0;
            $usersReport[$personId]['totalHours'] += $personData['totalWorkingTime'] ?? 0;
            $usersReport[$personId]['totalDelay'] += $personData['totaldelay'] ?? 0;
        }
    }



    return view('report.report-all-months', [
        'usersReport' => $usersReport,
        'months' => array_values($workingMonth),
    ]);



    }
}
