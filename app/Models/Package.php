<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'client_id',
        'name',
        'months',
        'price_amd',
        'is_active',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'months'    => 'integer',
        'price_amd' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope՝ միայն ակտիվ փաթեթները
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Package → Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * People (այցելուներ) կապը
     * մեկ փաթեթ → շատ մարդիկ
     */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class, 'package_id');
    }

    /**
     * Package ↔ Discounts (pivot: discount_package)
     * Մեկ package կարող է ունենալ մի քանի զեղչ
     */
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Discount::class,
            'discount_package',
            'package_id',
            'discount_id'
        )-> withTimestamps();
    }

    /**
     * Միայն ակտիվ զեղչեր այս package-ի համար
     * (պետք է Discount model-ում ունենաս scopeActive())
     */
    public function activeDiscounts(): BelongsToMany
    {
        return $this->discounts()->active();
    }
}
