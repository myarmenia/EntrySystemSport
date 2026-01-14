<?php
 namespace App\Services;

use App\Interfaces\AttendanceSheetInterface;
use App\Interfaces\AttendanceSheetTimeInterface;

class AttendanceSheetTimeService
{
    public function __construct(protected AttendanceSheetTimeInterface $attendanceSheetTimeRepository){}


    public function store($tb_name,$person_id,$client_id,$direction,$date,$day,$time, $existingTime){

        $data = $this->attendanceSheetTimeRepository->store($tb_name,$person_id,$client_id,$direction,$date,$day,$time, $existingTime);
        return $data;
    }
}
