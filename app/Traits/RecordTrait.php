<?php
namespace App\Traits;

use App\Mail\MyMail;
use App\Models\ScheduleDetails;
use App\Models\Turnstile;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Mail;

trait RecordTrait
{
    public function getPersonWorkingHours($peopleDailyRecord,$records, $peopleId,$day){
            // dd($peopleDailyRecord,$records, $peopleId,$day);
            $totalSeconds = 0;
            $entryTime = null;  // To store the entry time
            $exitTime = null;
            $working_array = [];
            // dd($day);
            // dd($records->values());
            $recordsArray = $records->map(function ($item) {
                        return [
                            'date' => $item->date,
                            'direction' => $item->direction
                        ];
                    });
                    // dump($recordsArray);
                    // dd($recordsArray);
                    // $peopleDailyRecord[$peopleId][$day] = (array) ($peopleDailyRecord[$peopleId][$day] ?? []);

                    if (!$recordsArray->contains('direction', 'exit') && $recordsArray->contains('direction', 'enter')) {

                        $peopleDailyRecord[$peopleId][$day]['exit'] ="Անձի ելքը չի գրանցվել";


                    }
                    if (!$recordsArray->contains('direction', 'enter')  && $recordsArray->contains('direction', 'exit')) {

                        $peopleDailyRecord[$peopleId][$day]['enter'] ="Անձի մուտքը չի գրանցվել";

                    }
            // dd($records);
            $key1 = 0;
            $records_sorted_by_date = $records;
            // dd($records_sorted_by_date);

            $records=$records->values();
            // dd($records);
            // dd("$records2",$records2[1],"$records",$records[1]);
            foreach ($records_sorted_by_date as $record) {
                // dump($key1);
                // if($key1===2){

                    if($record->direction == "unknown"){
                        $find_mac_direction = Turnstile::where('mac',$record->mac)->value('direction');

                        $record->direction = $find_mac_direction;

                    }
                        // dump($key1, $entryTime);
                    if ($record->direction == 'enter' && !$entryTime ) {
                        // dump('key-enter', $records[$key1]);

                        $entryTime  = Carbon::parse($record->date);
                        //  nor em avelacrel wor array chi darzni array
                        if (!isset($peopleDailyRecord[$peopleId][$day]['enter']) || !is_array($peopleDailyRecord[$peopleId][$day]['enter'])) {
                            $peopleDailyRecord[$peopleId][$day]['enter'] = [];
                        }

                        $peopleDailyRecord[$peopleId][$day]['enter'][]= Carbon::parse($record->date)->format('H:i');

                    }

                    if ($record->direction === 'exit' && $entryTime) {
                        // dump('key-elseif', $records[$key1]);

                        $peopleDailyRecord[$peopleId][$day]['exit'][] = Carbon::parse($record->date)->format('H:i');

                        $exitTime = Carbon::parse($record->date);

                        $entry = explode(' ', $entryTime->toTimeString())[0];
                        // dd($entry);
                        $entryT = Carbon::createFromFormat('H:i:s', $entry);
                        // dd($entryT);


                        $exit = explode(' ', $exitTime->toTimeString())[0];
                        $exitT = Carbon::createFromFormat('H:i:s', $exit);
                        // dump($exitT, $entryT);

                            if ($exitT->greaterThan($entryT)) {
                            // dump($key1,'exitT',$exitT,'entryT', $entryT);
                            //   dump( $entryT, $exitT);
                                $interval = $exitT->diff($entryT);

                                $peopleDailyRecord[$peopleId][$day]['working_times'][] = $interval->format('%H:%I:%S');

                                if (isset($records[$key1 + 1]) && $records[$key1 + 1]->direction == "enter") {
                                    // dd($key1 + 1);
                                    $entryTime = null;

                                }

                                if( (isset($records[$key1 - 1]) && $records[$key1 - 1]->direction == "exit") ){
                                //   dump($key1,$record->);
                                  $interval = $exitT->diff($entryT);

                                    array_pop($working_array);


                                }

                                $working_array[] = $interval->format('%H:%I:%S');

                                // dump(444,$working_array);
                            }else {

                                $exitT->addDay();
                                // dd($exitT, $entryT);
                                $interval = $exitT->diff($entryT);
                                // dd($interval);
                                // dump("exit" ,$entryT, $exitT);
                                $peopleDailyRecord[$peopleId][$day]['working_times'][] = $interval->format('%H:%I:%S');
                                // dump(77,$records);
                                if (isset($records[$key1 + 1]) && $records[$key1 + 1]->direction == "enter") {
                                    // dump(888,$records[$key1 + 1]);

                                    $entryTime = null;

                                }
                                // dump('key-1',$records[$key1 - 1]);
                                // dump('key', $records[$key1]);
                                if( (isset($records[$key1 - 1]) && $records[$key1 - 1]->direction == "exit") ){

                                    // $totalSeconds = 0;
                                    array_pop($working_array);

                                }
                                $working_array[] = $interval->format('%H:%I:%S');
                                // dump(555,$working_array);

                            }

                            // dump($working_array);




                    }
                // } //if($key1==2)


                $key1++;


            }

            // dump($working_array);
// dd($working_array);
            foreach($working_array as $ar){
                // dd($ar);
                // dd($ar->format('%H:%I:%S'));

                list($hours, $minutes, $seconds) = explode(':',  $ar);
                $totalSeconds += $hours * 3600 + $minutes * 60 + $seconds;
                // dd($totalSeconds);

            }
            // dd($totalSeconds);
            $totalMinutes = intdiv( $totalSeconds, 60); // Общее количество минут
            // dd( $totalMinutes);
            $hours = intdiv($totalMinutes, 60); // Количество часов
            $minutes = $totalMinutes % 60; // Оставшиеся минуты
            $peopleDailyRecord[$peopleId][$day]['daily_working_times'] = "{$hours} ժ {$minutes} ր";
            $peopleDailyRecord[$peopleId][$day]['daily_working_times_totalSeconds'] = $totalSeconds;
        //     //    dd($peopleDailyRecord);


        // dump($peopleDailyRecord);
        return ($peopleDailyRecord);
    }





}
