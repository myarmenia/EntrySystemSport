<?php
namespace App\Traits;

use App\Helpers\MyHelper;
use App\Models\AttendanceSheet;
use App\Models\Client;
use App\Models\ScheduleDepartmentPerson;
use App\Models\ScheduleDetails;
use App\Models\Turnstile;
use App\Traits\RecordTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

trait ReportFilterTrait{
    use ScheduleIntervalTrait, RecordTrait, CalculateTotalHoursTrait;
    // abstract function model();

 public function filter($data)
  {
                  // dd($data);
          $attendance_sheet = $data['attendance_sheet'];
              // dd( $attendance_sheet);
          $groupedEntries = $this->getEntriesByScheduleInterval($attendance_sheet);
                // dd($groupedEntries);
          $peopleDailyRecord =[];
              // dd($groupedEntries);
            foreach ($groupedEntries as $peopleId => $dailyRecords) {
                // dump($peopleId);

                foreach ($dailyRecords as $date => $records) {
                    // dd($date);   //"2025-03-20"
                    // dd($records);
                    // $date == "2025-03-22" &&
                    // if( $date == "2025-03-14" && $peopleId==72 ){
                        // dd(77);

                      $day = date('d',strtotime($date));
                    //   dd($day);//20

                      // $day=11;
                      $records = $records->sortBy('date')->unique('date'); // Ensure records are sorted by time
                      // dd( $records );
                      // $entryTime = null;

                      // վերադարձնում է ամսվա այդ օրը շաբաթվա ինչ օր է
                      $dayOfWeek = Carbon::parse(time: $date)->format('l');
                    //    dd($date,$dayOfWeek);//Thursday
                       // dd($records);


                      $peopleDailyRecord = $this->getPersonWorkingHours($peopleDailyRecord,$records, $peopleId,$day);
                    //    dd($peopleDailyRecord);
                        //  dump( $peopleDailyRecord);
                      // dump($peopleDailyRecord);

                      $worker_first_enter = $records->first();
                        //  dd( $worker_first_enter);
                      // dd($worker_first_enter->schedule_name_id);

                      $schedule_id = $worker_first_enter->schedule_name_id;
                        //  dd($schedule_id);
                      $clientWorkingTimes = ScheduleDetails::where('schedule_name_id',$schedule_id)
                                                         ->get()
                                                         ->keyBy('week_day');
                        //  dd( $clientWorkingTimes);
                      // dd( $date,$dayOfWeek);
                      $clientSchedule = $clientWorkingTimes[$dayOfWeek] ?? null;
                    //   dd($clientSchedule);
                        if(isset($clientSchedule)){
                            if($worker_first_enter->direction == "enter"){
                                $get_client_week_working_start_time='';
                                $get_client_week_working_end_time='';

                                if(isset($clientSchedule->day_start_time) && $clientSchedule->day_start_time!=null){

                                    $get_client_week_working_start_time = new DateTime($clientSchedule->day_start_time);
                                    // dd($get_client_week_working_start_time);
                                }
                                if( isset($clientSchedule->day_start_time) && $clientSchedule->day_end_time!=null){

                                    $get_client_week_working_end_time = new DateTime($clientSchedule->day_end_time);
                                }
                                //  dd($get_client_week_working_end_time);
                                //  dd($worker_first_enter->date); //"2025-03-20 10:05:38"
                                $worker_first_enter_time = explode(' ', string: $worker_first_enter->date)[1];
                                 // dd($worker_first_enter_time); //"10:05:38"
                                $worker_first_enter_day = explode(' ', string: $worker_first_enter->date)[0];
                                // dd($worker_first_enter_day);
                                $worker_first_enter_time = new DateTime($worker_first_enter_time);
                                 // աշխատակցի առաջին մուտքի ժամը  փոքր է գործատուի տվյալ օրվա աշխատանքային ավարտի ժամից
                                if($worker_first_enter_time < $get_client_week_working_end_time){
                                    // dd($worker_first_enter_time,$get_client_week_working_end_time);
                                        //   dd($worker_first_enter_time,$get_client_week_working_start_time);
                                          // աշխատակիցը ուշացել է, աշխատակցի առաջին մուտքը մեծ է գործատուի շաբաթվա տվյալ օրվա աշխատանքի սկսման  օրվանից
                                        //   if($worker_first_enter_time>$get_client_week_working_start_time){
                                        if($worker_first_enter_time<$get_client_week_working_start_time || $worker_first_enter_day!=$date){
                                        //    dd($get_client_week_working_start_time);

                                            if($worker_first_enter_time<$get_client_week_working_start_time && $worker_first_enter_day==$date){
                                                $interval = $worker_first_enter_time->diff($get_client_week_working_start_time);
                                            }else{
                                                $time1 = Carbon::parse($worker_first_enter_time); // 18:00-09:00// first enter 01:00
                                                $time1->addDay(); // Добавляем 24 часа


                                                $interval = $get_client_week_working_start_time->diff($time1);
                                            }
                                            // $interval = $worker_first_enter_time->diff($get_client_week_working_start_time);
                                            $peopleDailyRecord[$peopleId][$day]['delay_hour'][]=$interval->format('%H:%I:%S');
                                            $peopleDailyRecord[$peopleId][$day]['delay_display']=true;
                                            $peopleDailyRecord[$peopleId][$day]['coming']=true;

                                        }else{
                                           $peopleDailyRecord[$peopleId][$day]['coming']=true;

                                        }
                                        // ============
                                        // dd($records);
                                        // dd($clientSchedule->break_start_time,  $clientSchedule->break_end_time);
                                    $breakfastInterval = $records
                                                    ->filter(function ($record) use ($clientSchedule) {
                                                        $recordTime = (new DateTime($record->date))->format('H:i:s');
                                                            return $recordTime >= $clientSchedule->break_start_time && $recordTime <= $clientSchedule->break_end_time;
                                                        })
                                                        ->sortByDesc('date') // Sort by date in descending order
                                                        ->groupBy('direction') // Group records by 'direction'
                                                        ->map(function ($group) {
                                                            return $group->first()->date; // Take the first (latest) record's date from each group
                                                        });
                                    // dd($breakfastInterval);
                                    $breakfastInterval_find_mac = $records
                                                                ->filter(function ($record) use ($clientSchedule) {
                                                                $recordTime = (new DateTime($record->date))->format('H:i:s');
                                                                    return $recordTime >= $clientSchedule->break_start_time && $recordTime <= $clientSchedule->break_end_time;
                                                                })
                                                                ->sortByDesc('date') // Sort by date in descending order
                                                                ->groupBy('direction') // Group records by 'direction'
                                                                ->map(function ($group) {
                                                                    return $group->first()->mac; // Take the first (latest) record's date from each group
                                                                });
                                                        //    dd($breakfastInterval_find_mac);
                                                                $ushacum = false;
                                                                // dd($breakfastInterval);
                                                                if(count($breakfastInterval)>0){
                                                                    // dd(888);
                                                                    if(count($breakfastInterval)==1 && isset($breakfastInterval["exit"])){

                                                                        $ushacum = true;

                                                                    }
                                                                    if(count($breakfastInterval)>1 ){



                                                                        // dump( $peopleId, $breakfastInterval,$breakfastInterval_find_mac);
                                                                        $enterTime='';
                                                                        $exitTime = '';
                                                                        if(isset($breakfastInterval_find_mac['unknown'])){
                                                                            $turnstile=Turnstile::where('mac',$breakfastInterval_find_mac['unknown'])->first();

                                                                            if($turnstile){
                                                                                if($turnstile->direction == "exit"){
                                                                                    $exitTime = new DateTime($breakfastInterval['unknown']);
                                                                                    // dump($exitTime);

                                                                                }
                                                                                else{
                                                                                    $enterTime = new DateTime($breakfastInterval['unknown']);
                                                                                }
                                                                            }
                                                                        }else{

                                                                            $enterTime = new DateTime($breakfastInterval['enter']);
                                                                            if(isset($breakfastInterval['enter'])){
                                                                                    if(isset($breakfastInterval['exit'])){
                                                                                        $exitTime = new DateTime($breakfastInterval['exit']);

                                                                                    }
                                                                            }

                                                                            //   dump( $peopleId, $breakfastInterval,$breakfastInterval_find_mac);


                                                                        }
                                                                        if(isset($enterTime) && isset($exitTime)){
                                                                            // dump($peopleId, $enterTime, $exitTime);
                                                                            if ($exitTime > $enterTime) {
                                                                                        $ushacum = true;
                                                                                    }
                                                                        }


                                                                    }



                                                                }else if($clientSchedule->day_start_time < $clientSchedule->day_end_time){
                                                                    // dd(777);
                                                                    // dd($clientSchedule);

                                                                    $firstActionAfterBreakfast = $records
                                                                                    ->filter(function ($record) use ($peopleId, $clientSchedule,$day) {
                                                                                        // Parse the date using Carbon and format it to 'H:i:s' (hours:minutes:seconds)
                                                                                        $recordTime = Carbon::parse($record->date)->format('H:i:s');
                                                                                        // dump($day, $peopleId, $recordTime, $clientSchedule->break_end_time);
                                                                                        // Check if the direction is 'enter', the time is after $clientSchedule->break_end_time, and people_id is $peopleId
                                                                                        return $record->direction === 'enter' && $recordTime >= $clientSchedule->break_end_time && $record->people_id == $peopleId;
                                                                                    })
                                                                                    ->sortBy('date') // Sort by date in ascending order
                                                                                    ->first();
                                                                                    // dump( $peopleId,$firstActionAfterBreakfast);

                                                                    // dd($firstActionAfterBreakfast);
                                                                        if( isset($firstActionAfterBreakfast->direction) && $firstActionAfterBreakfast->direction=="enter"){
                                                                            $ushacum=true;
                                                                            // dump($peopleId,"after",$day, $firstActionAfterBreakfast);
                                                                        }
                                                                        // dd($firstActionAfterBreakfast);



                                                                }
                                                                if($ushacum == true){
                                                                    // dd($ushacum);
                                                                    // dd($peopleId, $date,$day, $clientSchedule, $peopleDailyRecord);


                                                                    $peopleDailyRecord=$this->ushacum_arm($peopleId, $date,$day, $clientSchedule, $peopleDailyRecord);
                                                                    //   dd($peopleDailyRecord);
                                                                }



                                }//if($worker_first_enter_time < $get_client_week_working_end_time){



                            }else{ // if($worker_first_enter->direction == "enter")

                                $peopleDailyRecord[$peopleId][$day]['anomalia']=true;

                            }

                        }//if(isset($clientSchedule)){




                    // }//date


                }//foreach ($dailyRecords as $date => $records)
            }
            // dd($peopleDailyRecord);
            if(isset($peopleDailyRecord)){

                $client = Client::where('id', $data['client_id'])->first();
                // dd($peopleDailyRecord);
                $total_monthly_working_hours = $this->calculate($peopleDailyRecord,$client);
                // dd( $total_monthly_working_hours);

                $routeName = Route::currentRouteName();
                // dd( $routeName);
                if($routeName=="export-xlsx-armobil"){

                    // $total_monthly_working_hours['mounth'] = $month;
                    $total_monthly_working_hours['mounth'] = $data['month'];

                }
                // dd($total_monthly_working_hours);

                return  $peopleDailyRecord = $total_monthly_working_hours ?? null;

            }else{
                return false;
            }


  }

    public function ushacum_arm($peopleId, $date,$day, $clientSchedule, $peopleDailyRecord){



            $firstAfter1400 = DB::table('attendance_sheets')
                ->where('direction', 'enter')
                ->where('people_id', $peopleId)
                ->whereDate('date', date('Y-m-d', strtotime($date)))
                ->whereTime('date', '>', $clientSchedule->break_end_time) // Время после 14:00
                ->orderBy('date', 'asc') // Сортируем по времени
                ->first();
                    // dump($firstAfter1400);
                    // dd($firstAfter1400);

                if($firstAfter1400){


                    $firstAfter1400_datePart = explode(' ', $firstAfter1400->date)[1];
                    // dd($firstAfter1400_datePart); //"14:10:38"


                                $firstAfter1400_time1 = new DateTime($firstAfter1400_datePart);

                                $firstAfter1400_time2 = new DateTime($clientSchedule->break_end_time);

                                $firstAfter1400_interval = $firstAfter1400_time1 ->diff($firstAfter1400_time2);

                                // dump($firstAfter1400_interval->format('%H h %I m'));
                                // dd($firstAfter1400_interval);

                    if($firstAfter1400_interval->format('%H h %I m')!=="00 h 00 m"){
                        // եթե աշխատողի մուտքը ընդմիջումից հետո  մտել է 14։10։38
                        // dd($firstAfter1400_interval->format('%H:%I:%S')); //"00:10:38"

                        $peopleDailyRecord[$peopleId][$day]['delay_hour'][]= $firstAfter1400_interval->format('%H:%I:%S');
                        $peopleDailyRecord[$peopleId][$day]['delay_display']=true;
                        // dump($peopleDailyRecord);
                        // dd($peopleDailyRecord);

                    }

                }

                // dd($peopleDailyRecord);

                return  $peopleDailyRecord;
    }



}
