<?php
namespace App\Traits;

use App\Models\AttendanceSheet;
use App\Models\Client;
use App\Models\ClientWorkingDayTime;
use App\Models\Staff;
use App\Models\Turnstile;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

trait ReportTrait{

    public function report($mounth){
        // dd($mounth);

        if(Auth::user()->hasRole('client_admin')){
            $client_id = Auth::id();

        }
        if(Auth::user()->hasRole('manager')){
            // dd(777);
            $client_admin_id=Staff::where('user_id',Auth::id())->value('client_admin_id');
            $client = Client::find($client_admin_id);
            $client_id = $client->user_id;
        }
        // $client = Client::where('user_id', Auth::id())->with('people.attendance_sheets')->first();
        $client = Client::where('user_id', $client_id)->with('people.attendance_sheets')->first();
// dd($client->people);
        if($mounth!=null){

            [$year, $month] = explode('-', $mounth);

            $monthDate = Carbon::createFromDate($year, $month, 1);

            $startOfMonth =  $monthDate->startOfMonth()->toDateTimeString();
            $endOfMonth =  $monthDate->endOfMonth()->toDateTimeString();


            // if(count($client->people->pluck('id')->toArray())==0){
            //     throw new Exception("Աշխատակազմը բացակայում է");

            // }
            $attendance_sheet = AttendanceSheet::whereBetween('date', [$startOfMonth, $endOfMonth])
                                ->whereIn("people_id", $client->people->pluck('id')->toArray())
                                ->orderBy('people_id')
                                ->orderBy('date')
                                ->get();

                                // dd($attendance_sheet);




                $groupedEntries = $attendance_sheet->groupBy(['people_id', function ($oneFromCollection) {
                    return Carbon::parse($oneFromCollection->date)->toDateString();
                }]);
                // dd( $groupedEntries);


                $clientWorkingTimes = DB::table('client_working_day_times')
                                    ->where('client_id', $client->id)
                                    ->get()
                                    ->keyBy('week_day');

                $peopleDailyRecord=[];

                foreach ($groupedEntries as $peopleId => $dailyRecords) {

                    foreach ($dailyRecords as $date => $records) {


                        $day=date('d',strtotime($date));

                        $records = $records->sortBy('date')->unique('date'); // Ensure records are sorted by time

                        $entryTime = null;
                        $dailyWorkingTime = 0; // Секунды
                        $dayOfWeek = Carbon::parse(time: $date)->format('l');
                        // dd($date);
                        $clientSchedule = $clientWorkingTimes[$dayOfWeek] ?? null;
                        // dd($clientSchedule);


                        foreach ($records as $record) {

                            if($record->direction == "unknown"){
                                $find_mac_direction = Turnstile::where('mac',$record->mac)->value('direction');

                                $record->direction = $find_mac_direction;
                            }


                            if ($record->direction == 'enter') {

                                $entryTime  = Carbon::parse($record->date);


                            } elseif ($record->direction == 'exit' && $entryTime) {


                                $exitTime = Carbon::parse($record->date);

                                    $entry = explode(' ', $entryTime->toTimeString())[0];
                                    $entryT = Carbon::createFromFormat('H:i:s', $entry);


                                    $exit = explode(' ', $exitTime->toTimeString())[0];
                                    $exitT = Carbon::createFromFormat('H:i:s', $exit);

                                    // dump($entryT, $exitT );

                                    // Если вход и выход в один день, добавляем разницу
                                    if ($exitT->greaterThan($entryT)) {

                                        $interval = $exitT->diff($entryT);

                                        $peopleDailyRecord[$peopleId][$day]['working_times'][] = $interval->format('%H:%I:%S');



                                    }

                                // Сбрасываем время входа после расчета
                                $entryTime = null;


                            }


                        }

                        $worker_first_enter = $records->first();
                        if(isset($clientSchedule)){

                            if($worker_first_enter->direction=="enter"){


                                    // dd($clientSchedule);
                                    $get_client_week_working_start_time='';
                                    $get_client_week_working_end_time='';
                                    if(isset($clientSchedule->day_start_time) && $clientSchedule->day_start_time!=null){
                                        $get_client_week_working_start_time = new DateTime($clientSchedule->day_start_time);

                                    }
                                    if( isset($clientSchedule->day_start_time) && $clientSchedule->day_end_time!=null){
                                        $get_client_week_working_end_time = new DateTime($clientSchedule->day_end_time);

                                    }
                                    // dd($get_client_week_working_end_time);
                                    $worker_first_enter_time = explode(' ', $worker_first_enter->date)[1];
                                    // dump($peopleId, $day, $worker_first_enter_time);

                                    $worker_first_enter_time = new DateTime($worker_first_enter_time);
                                    // dd($get_client_week_working_end_time, $worker_first_enter_time);

                                        if($worker_first_enter_time<$get_client_week_working_end_time){

                                            // dd($worker_first_enter_time,$get_client_week_working_start_time);
                                            if($worker_first_enter_time>$get_client_week_working_start_time){
                                                // dd($get_client_week_working_start_time);

                                                $interval = $worker_first_enter_time->diff($get_client_week_working_start_time);

                                                $peopleDailyRecord[$peopleId][$day]['delay_hour'][]=$interval->format('%H:%I:%S');
                                                $peopleDailyRecord[$peopleId][$day]['delay_display']=true;
                                                $peopleDailyRecord[$peopleId][$day]['coming']=true;


                                            }
                                            else{
                                                $peopleDailyRecord[$peopleId][$day]['coming']=true;
                                            }

                                            // =================
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
                                            // dump( $peopleId, $breakfastInterval,$breakfastInterval_find_mac);



                                            $ushacum = false;
                                            // dd($breakfastInterval);
                                            if(count($breakfastInterval)>0){
                                                // dump($peopleId,$breakfastInterval);

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


                                            }
                                            else{


                                                if($clientSchedule->week_day!="Saturday"){
                                                    // dd($records);
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


                                                    if( isset($firstActionAfterBreakfast->direction) && $firstActionAfterBreakfast->direction=="enter"){
                                                        $ushacum=true;
                                                        // dump($peopleId,"after",$day, $firstActionAfterBreakfast);
                                                    }
                                                }
                                            }


                                                if($ushacum == true){

                                                    $peopleDailyRecord=$this->ushacum($peopleId, $date,$day, $clientSchedule, $peopleDailyRecord);

                                                }
                                                // =================


                                        }
                                        else{
                                                // dump($peopleId);
                                                $peopleDailyRecord[$peopleId][$day]['anomalia']=true; // gorci jamic heto e eke

                                            }

                                         // dd($peopleDailyRecord);



                            }
                            else{

                                    $peopleDailyRecord[$peopleId][$day]['anomalia']=true;
                                }
                        } //if(isset($clientSchedule)


                    }


                    // dd($peopleDailyRecord);

                }


        }

            if(isset($peopleDailyRecord)){
                $total_monthly_working_hours = $this->calculate($peopleDailyRecord,$client);

                $routeName = Route::currentRouteName();
                if($routeName=="export-xlsx"){
                    $total_monthly_working_hours['mounth']=$month;

                }
                // dd($total_monthly_working_hours);

                return  $peopleDailyRecord = $total_monthly_working_hours ?? null;

            }else{
                return false;
            }







    }
    public function ushacum($peopleId, $date,$day, $clientSchedule, $peopleDailyRecord){



            $firstAfter1400 = DB::table('attendance_sheets')
                ->where('direction', 'enter')
                ->where('people_id', $peopleId)
                ->whereDate('date', date('Y-m-d', strtotime($date)))
                ->whereTime('date', '>', $clientSchedule->break_end_time) // Время после 14:00
                ->orderBy('date', 'asc') // Сортируем по времени
                ->first();
                    // dump($firstAfter1400);

                if($firstAfter1400){


                    $firstAfter1400_datePart = explode(' ', $firstAfter1400->date)[1];


                                $firstAfter1400_time1 = new DateTime($firstAfter1400_datePart);

                                $firstAfter1400_time2 = new DateTime($clientSchedule->break_end_time);

                                $firstAfter1400_interval = $firstAfter1400_time1 ->diff($firstAfter1400_time2);

                                // dump($firstAfter1400_interval->format('%H h %I m'));
                                // dd($firstAfter1400_interval);

                    if($firstAfter1400_interval->format('%H h %I m')!=="00 h 00 m"){

                        $peopleDailyRecord[$peopleId][$day]['delay_hour'][]= $firstAfter1400_interval->format('%H:%I:%S');
                        $peopleDailyRecord[$peopleId][$day]['delay_display']=true;
                        // dump($peopleDailyRecord);

                    }

                }


                return  $peopleDailyRecord;
    }

    public function calculate($peopleDailyRecord,$client){


        foreach ($peopleDailyRecord as $personId => $records) {
            $totalSeconds = 0;
            $delaytotalSeconds =0;
            // dd($records);

            // Iterate through each person's records
            foreach ($records as $key => $data) {
                // dump($data);
                if (isset($data['working_times'])) {
                    foreach ($data['working_times'] as $time) {
                        // dump($time);
                        // Convert each time string (HH:MM:SS) to seconds
                        list($hours, $minutes, $seconds) = explode(':', $time);
                        $totalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
                    }
                }
                if (isset($data['delay_hour'])) {
                    foreach ($data['delay_hour'] as $delay) {
                        // Convert each time string (HH:MM:SS) to seconds
                        list($hours, $minutes, $seconds) = explode(':', $delay);
                        $delaytotalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
                    }
                }

            }

            // Convert total seconds back to hours, minutes, and seconds
            $totalHours = floor($totalSeconds / 3600);
            $totalSeconds %= 3600;
            $totalMinutes = floor($totalSeconds / 60);
            $totalSeconds %= 60;

            $delaytotalHours = floor($delaytotalSeconds / 3600);
            $delaytotalSeconds %= 3600;
            $delaytotalMinutes = floor($delaytotalSeconds / 60);
            $delaytotalSeconds %= 60;

            $peopleDailyRecord[$personId]['totalMonthDayCount'] =count($records);
            // Format the result into HH:MM:SS
            $peopleDailyRecord[$personId]['totalWorkingTimePerPerson'] = sprintf('%d ժ, %d ր', $totalHours, $totalMinutes, $totalSeconds);
            $peopleDailyRecord[$personId]['totaldelayPerPerson'] = sprintf('%d ժ, %d ր', $delaytotalHours, $delaytotalMinutes, $delaytotalSeconds);
            // dd($peopleDailyRecord);
            // dd($client->working_time,$totalHours);
            $clientWorkingHours = (float) $client->working_time; // Convert string to float
            $personWorkingHours = (float) $totalHours;
            if($clientWorkingHours>$personWorkingHours){
                $peopleDailyRecord[$personId]['personWorkingTimeLessThenClientWorkingTime'] =true;

            }
        }
        // dd($peopleDailyRecord);
        return  $peopleDailyRecord;


    }
}
