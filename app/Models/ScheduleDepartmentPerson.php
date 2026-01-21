<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDepartmentPerson extends Model
{
    use HasFactory;
    protected $table = 'schedule_department_people';

    protected $guarded = [];


    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
    public function schedule_name()
    {
        return $this->belongsTo(ScheduleName::class, 'schedule_name_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }
    public function sessionDuration()
    {
        return $this->belongsTo(\App\Models\SessionDuration::class, 'session_duration_id');
    }
}
