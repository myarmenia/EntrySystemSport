<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PersonPayment extends Model
{
    protected $fillable = [
        'person_id',
        'client_id',
        'payment_method',
        'payment_bank',
        'amount_amd',
        'currency',
        'status',
        'paid_at',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
