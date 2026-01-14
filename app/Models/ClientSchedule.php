<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function schedule_name(){

        return $this->belongsTo(ScheduleName::class,'schedule_name_id');
    }
    public function getScheduleDetailsAttribute(){

        return $this->schedule_name && $this->schedule_name->schedule_details->isNotEmpty()?
                $this->schedule_name->schedule_details: null;
    }
}
