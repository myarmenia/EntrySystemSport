<?php

namespace App\Models;

use App\Helpers\MyHelper;
use App\Traits\ReportFilterTrait;
use App\Traits\ReportTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSheet extends Model
{
    use HasFactory;

    protected $guarded =[];
    protected $table = 'attendance_sheets';
    protected $filterFields = ['people_id','date'];
    protected $filterFieldsInRelation = ['name'];
    protected $appends = ['schedule_name_id', 'department_id'];

    public function people(): BelongsTo{

        return $this->belongsTo(Person::class,'people_id');
    }

    // accesors
    public function getScheduleNameIdAttribute()
    {
           return $this->people && $this->people->schedule_department_people->isNotEmpty()
          ? $this->people->schedule_department_people->first()->schedule_name_id
          : null;
    }
    public function getScheduleDetailsAttribute()
    {
        return $this->people && $this->people->schedule_department_people->isNotEmpty()
          ? $this->people->schedule_department_people->first()->schedule_name?->schedule_details
          : null;

    }
    public function getDepartmentIdAttribute()
    {
        return $this->people && $this->people->schedule_department_people->isNotEmpty()
        ? $this->people->schedule_department_people->first()->department_id
        : null;
    }

    public function scopeForClient(Builder $query, $month, $departmentId=null)
    {
        // dd($departmentId);
// dd($query);
        $clientId = MyHelper::find_auth_user_client();
        // dd($clientId);//14

         [$year, $month] = explode('-', $month);

            $monthDate = Carbon::createFromDate($year, $month, 1);
            // dd($monthDate);

            $startOfMonth =  $monthDate->startOfMonth()->toDateTimeString();
            $endOfMonth =  $monthDate->endOfMonth()->toDateTimeString();
            // dd($startOfMonth,$endOfMonth);


        return $query
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->whereHas('people', function ($q) use ($clientId, $departmentId) {
            $q->where('client_id', $clientId)
              ->whereHas('schedule_department_people', function ($subQ) use ($clientId, $departmentId) {
                  // Фильтруем по `client_id` и `department_id`
                  $subQ->where('client_id', $clientId);
                    if ($departmentId) {
                        $subQ->where('department_id', $departmentId);
                    }

              });
        })
        ->with(['people.schedule_department_people']);

    }
    public static function forPersonOnDate($personId, $date)
    {
        return self::where('people_id', $personId)
                ->whereDate('date', $date)
                ->get();
    }






}
