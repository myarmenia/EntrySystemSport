<?php
namespace App\Interfaces;

interface AttendanceSheetTimeInterface
{
    public function store($tb_name, $person_id, $client_id, $direction, $date, $day, $enterExitTime, $time);

}


