<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ScheduleDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded =[];

    public function schedule(){

        return $this->bolongsTo(ScheduleName::class, 'schedule_name_id');
    }

}
