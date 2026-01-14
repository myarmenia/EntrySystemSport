<?php

namespace App\Services;


use App\Helpers\MyHelper;
use App\Interfaces\AttendanceSheetInterface;
use App\Interfaces\CheckEntryCodeInterface;
use App\Interfaces\ClientIdFromTurnstileInterface;
use Illuminate\Http\Request;

class EntryExitSystemService
{
    public function __construct(
        protected ClientIdFromTurnstileInterface $turnstileRepository,
        protected CheckEntryCodeInterface $checkEntryCodeRepository,
        protected AttendanceSheetInterface $attendanceSheetRepository


    ) {
    }

    public function ees($data)
    {

        $result = null;
        $message = 'entry_coed is valid';

        $client_id = $this->turnstileRepository->getClientId($data->mac);

        if(!$client_id){
            \Log::info('client_id chka',["client id"=>$client_id]);

            $message = 'invalid mac';
        }
        else{

            $token = explode('#', $data->entry_code);

            $entry_code = count($token) > 1 ? $token[0] : null;
            $date_time = count($token) > 1 ? $token[1] : null;

            $entry_code = ($data->type === 'faceID' || $data->type === 'FaceID') ? MyHelper::binaryToDecimal($entry_code) : $entry_code;

            $checkEntryCode = $this->checkEntryCodeRepository->checkEntryCode($entry_code, $client_id);

            if (!$checkEntryCode->result) {
                $message = $checkEntryCode->message;
                //  \Log::info('!checkEntryCode->result',$message);
            } else {
                $data->date = date('Y-m-d H:i:s', $date_time);

                $person_permission = $checkEntryCode->result->active_person;
                $peopleId = $person_permission ? $person_permission->person_id : null;
                $data->people_id = $peopleId;
                $data->entry_code = $entry_code;


                $result = $this->attendanceSheetRepository->create($data->toArray());

            }
        }


        return (object) [
            'message' => $message,
            'result' => $result
        ];

    }

}
