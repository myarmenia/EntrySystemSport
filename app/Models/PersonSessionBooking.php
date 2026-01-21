<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonSessionBooking extends Model
{
    use HasFactory;

    protected $table = 'person_session_bookings';

    protected $fillable = [
        'client_id',
        'person_id',
        'trainer_id',
        'schedule_name_id',
        'department_id',
        'session_duration_id',
        'start_time',
        'end_time',
        'day',
        'session_start_time',
        'session_end_time',
        'package_months',
        'package_price_amd',
        'duration_price_amd',
        'duration_total_amd',
        'total_price_amd',
    ];


    protected $casts = [
        'session_start_time' => 'datetime',
        'session_end_time' => 'datetime',
    ];


    /* =======================
     | Relationships
     ======================= */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // մարզիչը user է
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function scheduleName()
    {
        return $this->belongsTo(ScheduleName::class, 'schedule_name_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function sessionDuration()
    {
        return $this->belongsTo(SessionDuration::class);
    }
}
