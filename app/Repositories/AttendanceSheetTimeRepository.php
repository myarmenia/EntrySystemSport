<?php
namespace App\Repositories;

use App\Interfaces\AttendanceSheetTimeInterface;
use App\Models\AttendanceSheet;
use App\Models\EntryCode;
use App\Models\PersonPermission;
use App\Models\ScheduleDepartmentPerson;
use App\Models\ScheduleDetails;
use Carbon\Carbon;
use Dotenv\Parser\Entry;

class AttendanceSheetTimeRepository implements AttendanceSheetTimeInterface
{
    public function store($tb_name, $person_id, $client_id, $direction, $date, $day, $enterExitTime, $time)
    {
        // dd($tb_name,$person_id,$client_id,$direction,$date,$day,$time,$existingTime);

        $db_time='';
        $compeare_direction ='';

        $fullDate =  $date."-".$day;
        // dd($fullDate);

        $fullTime = $time.":00"; //get time from request
        // dd($fullTime);

        // dd($formatted_time);

        $dayOfWeek = Carbon::parse(time: $fullDate)->format('l');
        $schedule_name_id = ScheduleDepartmentPerson::where(['client_id'=>$client_id,'person_id'=>$person_id])->value('schedule_name_id');
        $schedule_details = ScheduleDetails::where(['schedule_name_id'=>$schedule_name_id,'week_day'=>$dayOfWeek])->first();
        $clientDayEndTime = Carbon::parse($schedule_details->day_end_time); // client working end time
        $clientDayStartTime = Carbon::parse($schedule_details->day_start_time); // client working start time
        $workerEnterExitTime  = Carbon::parse($fullTime);      // աշխատակցի մուտքի կամ ելքի ժամանակն է
        $beforEnterExitTime = Carbon::parse($enterExitTime);   // աշխատակցի մուտքի կամ ելքի  բազայի ժամանակն է
        // dd("day_end_time",$clientDayEndTime , "day_start_time",$clientDayStartTime, $workerEnterExitTime);
        // dump($workerEnterExitTime);
        if($clientDayStartTime->lessThan($clientDayEndTime)){
            if ($workerEnterExitTime->between($clientDayStartTime, $clientDayEndTime)) {
                if($workerEnterExitTime->lessThan($beforEnterExitTime)){
                    if($direction=="exit"){
                        return "Մուտքագրված ժամը չի կարող մեծ լինել բազայում առկա մուտքի ժամից";

                    }
                }
            } else {

                return "Մուտքագրված ժամանակը չի համապատասխանում գործատուի աշխատանքային ժամանակացույցին";
            }
        }
        if ($clientDayStartTime->greaterThan($clientDayEndTime)) {

            $clientDayEndTime->addDay();

            if ($workerEnterExitTime ->isBetween($clientDayStartTime, $clientDayEndTime, true, true)  ) {


                if($workerEnterExitTime->isBefore($beforEnterExitTime)){

                    return "Մուտքագրված ժամը չի կարող մեծ լինել բազայում առկա մուտքի ժամից";
                }


            } else {
             

                $fullDate = Carbon::parse($fullDate); // 2025-03-22 00:00:00.0 որպեսզի կարողանանք օր ավելացնենք

                    // dd($fullDate);//2025-03-22 00:00:00.0 Asia/Yerevan (+04:00)
                // dd($fullTime);
                $fullTimeCarbon = Carbon::parse($fullTime)->format('H:i:s');
                 $midnight = Carbon::parse("00:00:00")->format('H:i:s');


                if($workerEnterExitTime->isBefore($beforEnterExitTime) &&  $fullTimeCarbon < $midnight ){

                    return "Մուտքագրված ժամը չի կարող մեծ լինել բազայում առկա մուտքի ժամից";
                }

                $fullDate = $fullDate->addDay()->format('Y-m-d'); //  օգտագործում եմ բազայում մուտք անելու ժամանակ
                // dd($fullDate,456);

                if ($workerEnterExitTime->format('H:i:s') > $clientDayEndTime->format('H:i:s')) {

                    return "Մուտքագրված ժամը չի համապատասխանում գործատուի աշխատանքային ժամանակացույցին";
                }

            }
        }


        $entry_code_id = PersonPermission::where('person_id',$person_id)->value('entry_code_id');
        // dd($entry_code);
        $entry_code_token = EntryCode::where('id',$entry_code_id)->value('token');

        $date = $fullDate." ".$fullTime;


        $store_data = [
            "people_id" => $person_id,
            "entry_code" => $entry_code_token,
            "date" => $date,
            "direction" => $direction
        ];
        $attendance_sheet =  AttendanceSheet::create($store_data);
        // dd($data);
        if($attendance_sheet){
            return "Գործողությունը հաստատված է:";
        }
    }
}
