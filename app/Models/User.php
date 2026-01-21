<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'schedule_name_id',
        'trainer_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peopleAsTrainer(): HasMany
    {
        return $this->hasMany(\App\Models\Person::class, 'trainer_id');
    }

    public function scheduleNames(): BelongsToMany
    {
        return $this->belongsToMany(ScheduleName::class, 'schedule_name_user')
            ->withTimestamps();
    }



    public function entry_codes(): HasMany
    {

        return $this->hasMany(EntryCode::class);
    }
    public function people(): HasMany
    {

        return $this->hasMany(Person::class);
    }
    public function turnstiles(): HasMany
    {

        return $this->hasMany(Turnstile::class);
    }
    public function staff(): HasMany
    {

        return $this->hasMany(Staff::class);
    }
    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function scheduleName(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ScheduleName::class, 'schedule_name_id');
    }

    // protected static function booted()
    // {
    //     static::deleting(function ($user) {
    //     //    $client_id= $user->client()->id;
    //     //     dd( $client_id);
    //     //     $deletedFolderPath = storage_path('app/people'. $client_id);
    //     //     if (File::exists($deletedFolderPath)) {
    //     //         dd(777);
    //     //         File::move($user->client()->image, $deletedFolderPath); // Move the folder
    //     //     }
    //         $user->client()->delete();

    //     });
    // }
    public function sessionDurations()
    {
        return $this->belongsToMany(\App\Models\SessionDuration::class, 'session_duration_user')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function sessionBookings()
    {
        return $this->hasMany(PersonSessionBooking::class, 'trainer_id');
    }
}
