<?php

namespace App\Models;

use App\Traits\ReportFilterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, ReportFilterTrait, SoftDeletes;

    protected $guarded =[];
    public function user(): BelongsTo{

        return $this->belongsTo(User::class,'user_id');
    }
    public function staff(): HasMany{

        return $this->hasMany(Staff::class);
    }
    public function person_position(): BelongsToMany{

        return $this->belongsToMany(PersonPosition::class);

    }
    public function people(): HasMany{
        return $this->hasMany(Person::class);
    }
    public function client_working_day_times(): HasMany{
        return $this->hasMany(ClientWorkingDayTime::class);
    }





}
