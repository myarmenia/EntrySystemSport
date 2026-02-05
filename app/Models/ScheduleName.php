<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleName extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function schedule_details(): HasMany
    {

        return $this->hasMany(ScheduleDetails::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'schedule_name_user')
            ->withTimestamps();
    }
    public function client_schedule_smokes(): HasMany{

        return $this->hasMany(ClientScheduleSmoke::class);
    }
}
