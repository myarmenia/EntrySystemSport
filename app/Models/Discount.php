<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Discount extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'client_id',
        'name',
        'type',      // percent | fixed
        'value',
        'starts_at',
        'ends_at',
        'status',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'status'    => 'boolean',
        'value'     => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    /* =========================
     |  RELATIONSHIPS
     |=========================*/

    /**
     * Discount → Client
     * (եթե client-ներով ես աշխատում)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Discount ↔ Packages (pivot: discount_package)
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(
            Package::class,
            'discount_package',
            'discount_id',
            'package_id'
        )->withTimestamps();
    }

    /* =========================
     |  SCOPES (օգտակար)
     |=========================*/

    /**
     * Միայն ակտիվ զեղչեր
     * status = 1
     * + date range valid
     */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 1)
            ->where(function ($qq) {
                $qq->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($qq) {
                $qq->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /* =========================
     |  HELPERS
     |=========================*/

    /**
     * Վերադարձնում է զեղչի արժեքը ըստ type-ի
     * (օրինակ՝ գնի հաշվարկի համար)
     */
    public function calculateDiscount(float $price): float
    {
        if ($this->type === 'percent') {
            return round($price * ($this->value / 100), 2);
        }

        return min($this->value, $price);
    }
}
