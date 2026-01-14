<?php
namespace App\Traits;

use Carbon\Carbon;

trait ScheduleIntervalTrait
{

    public function getEntriesByScheduleInterval($attendance_sheet)
    {


        $attendanceSheets =$attendance_sheet;
        // dd($attendanceSheets->where('people_id',75));
        $groupedEntries = $attendanceSheets->groupBy('people_id');
        // dd($groupedEntries);
        $monthlySchedule = [];

        foreach ($groupedEntries as $peopleId => $entries) {
            // dd($entries);
            // if($peopleId==75){
                // dd($entries);

                $personSchedules = [];

                foreach ($entries as $key=>$attendanceSheet) {
                    // if($key==0){
                        $scheduleDetailsArray = $attendanceSheet->getScheduleDetailsAttribute();
                        // dd($peopleId, $scheduleDetailsArray);
                        $attendanceDateTime = Carbon::parse($attendanceSheet->date);
                        // dd($attendanceDateTime); // date: 2025-03-20 18:05:38.0 Asia/Yerevan (+04:00)

                        // Фильтруем смену, которая подходит к времени прихода
                        $matchedSchedule = null;
                        // dd($scheduleDetailsArray);
                        if($scheduleDetailsArray!=null){
                            foreach ($scheduleDetailsArray as $scheduleDetails) {
                                // dd($scheduleDetails);
                                $scheduleId = $scheduleDetails['schedule_name_id'];
                                $attendanceDate = $attendanceDateTime->toDateString(); // YYYY-MM-DD
                                // dd( $attendanceDate);//"2025-03-20"
                                // Определяем начало и конец смены
                                $scheduleStart = Carbon::parse("{$attendanceDate} {$scheduleDetails['day_start_time']}");
                                $scheduleEnd = Carbon::parse("{$attendanceDate} {$scheduleDetails['day_end_time']}");
                                // dd(Carbon::parse($attendanceDateTime)->hour);
                                // Если смена идёт через ночь
                                // dump($key, $peopleId,$attendanceSheet, $scheduleDetails,$scheduleEnd, $scheduleStart);
                                // dd($attendanceDateTime->lt( $scheduleEnd->addDay()));
                                // 9<18 && 18>12
                                // dd("scheduleEnd",$scheduleEnd,'scheduleStart',$scheduleStart);
                                if ($scheduleEnd->lt($scheduleStart) && Carbon::parse($attendanceDateTime)->hour>12) {
                                    // dd(11,$scheduleEnd);
                                    $scheduleEnd->addDay();
                                }
                                elseif ($scheduleStart->lt($scheduleEnd)) {
                                    // dd(12);
                                    // Если $scheduleStart меньше $scheduleEnd, ничего не делаем
                                }
                                // elseif($scheduleEnd->lt($scheduleStart) && Carbon::parse($attendanceDateTime)->hour<12){
                                //     // dd(13);
                                //     // dd(Carbon::parse($attendanceDateTime)->hour);
                                // }
                                else{

                                    $scheduleStart->subDay();
                                }
                                // dd($scheduleEnd,$scheduleStart);
                                $earlyScheduleStart = $scheduleStart->copy()->subHours(2);
                                $extendedScheduleEnd = $scheduleEnd->copy()->addHours(2);

                                // Проверка, попадает ли запись на границу смены
                                if ($attendanceDateTime->gte($earlyScheduleStart) && $attendanceDateTime->lte($extendedScheduleEnd)) {

                                    $shiftDate = $scheduleStart->toDateString();
                                    if (!isset($personSchedules[$shiftDate])) {
                                        $personSchedules[$shiftDate] = collect(); // Создаем коллекцию
                                    }

                                    $personSchedules[$shiftDate]->push($attendanceSheet); // Добавляем объект модели
                                    break; // Берём первую подходящую смену и прекращаем проверку
                                }
                            }

                    // } if($key==0){

                    }

                }

                $monthlySchedule[$peopleId] = $personSchedules;
            // } // if($peopleId==75){
        }
     // dd($monthlySchedule);
        return $monthlySchedule;
    }


}
