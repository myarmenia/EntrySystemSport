<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionDuration extends Model
{
    protected $fillable = [
        'minutes',
        'title',
        'price_amd',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_active')
            ->withTimestamps();
    }
}
