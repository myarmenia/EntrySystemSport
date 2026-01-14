<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleName extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function schedule_details()
    {

        return $this->hasMany(ScheduleDetails::class);
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'schedule_name_user')
            ->withTimestamps();
    }
}
