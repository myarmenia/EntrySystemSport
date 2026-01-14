<?php
namespace App\Services;

use App\Mail\SendEmailClient;
use App\Models\AttendanceSheet;
use App\Models\Client;
use App\Models\Person;
use App\Models\ScheduleDepartmentPerson;
use App\Traits\ScheduleIntervalTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CronJobService
{
    use ScheduleIntervalTrait;

    public function get(){

        $current_day = \Carbon\Carbon::now(); // Carbon instance
        // $current_day="2025-03-13";
        $current_day = \Carbon\Carbon::parse("2025-04-11");
        $previous_day = $current_day->subDay()->format('Y-m-d'); // "2025-04-08"
        // dd($current_day);

        $scheduleDepartmentPerson = ScheduleDepartmentPerson::all()->whereNotNull('schedule_name_id')
                                                              ->pluck('person_id')
                                                              ->toArray();
        // dd($scheduleDepartmentPerson);
        $attendance_sheet = AttendanceSheet::whereIn('people_id',$scheduleDepartmentPerson)->get();

        $groupedEntries = $this->getEntriesByScheduleInterval($attendance_sheet);
        // Log::info("email sending client successfully1");
        // dd($current_day, $previous_day,$groupedEntries);
        foreach ($groupedEntries as $peopleId => $day) {
            // if($peopleId == 45){
                // Log::info("peopleId == 84");
                // Log::info("array_key_exists");
                // dd($previous_day);
                if (array_key_exists($previous_day, $day)) {
                    // Log::info("array_key_exists");

                    if ($day[$previous_day]) {
                        $recordsArray = $day[$previous_day];
                        // dd($peopleId, $day[$previous_day]);

                        if (!$recordsArray->contains('direction', 'exit') && $recordsArray->contains('direction', 'enter')) {
                            $person = Person::where('id',$peopleId)->first();

                            $client = Client::where('id',$person->client_id)->first();

                            // $client->email  "armine.khachatryan1982@gmail.com"
                            $data['client_email'] = $client->email;
                            $data['userFullName'] = $person->full_name;
                            $data['date'] = $previous_day;

                            Log::info("before email");
                            Mail::send(new SendEmailClient($data));

                        }else{
                            Log::info("else");
                        }
                    }
                }else{
                    Log::info(" not exist array_key_exists previous_day");
                }
            // }else{// if($peopleId == 45){
            //     Log::info("peopleId != 84");
            // }
        }

    }

}

