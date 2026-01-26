<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'trainer_id',
        'name',
        'description',
    ];
    protected $casts = [
        "trainer_id"  => 'int',
        "name"        => "string",
        "description" =>"string"
    ];

}
