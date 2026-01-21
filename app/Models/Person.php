<?php

namespace App\Models;

use Dom\Attr;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Person extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'people';

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function trainer()
    {
        return $this->belongsTo(\App\Models\User::class, 'trainer_id');
    }

    public function client(): BelongsTo
    {

        return $this->belongsTo(Client::class, 'client_id');
    }
    public function person_permission(): HasMany
    {
        return $this->hasMany(PersonPermission::class);
    }

    public function attendance_sheets(): HasMany
    {
        return $this->hasMany(AttendanceSheet::class, 'people_id');
    }
    public function activated_code_connected_person(): HasOne
    {
        return $this->hasOne(PersonPermission::class)->where('status', 1);
    }
    public function superviced()
    {
        return $this->hasOne(Superviced::class, 'people_id');
    }
    public function schedule_department_people()
    {

        return $this->hasMany(ScheduleDepartmentPerson::class);
    }



    public function absence()
    {

        return $this->hasMany(Absence::class);
    }

    // using in absence/edit.blade.php as accesors
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name . " " . $this->surname
        );
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function sessionBookings()
    {
        return $this->hasMany(PersonSessionBooking::class);
    }
}
