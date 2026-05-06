<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Moto extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'displacement_cc',
        'price',
        'short_description',
        'description',
        'image',
        'gallery',
        'highlights',
        'featured',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'gallery' => 'array',
        'highlights' => 'array',
        'featured' => 'boolean',
        'active' => 'boolean',
        'price' => 'decimal:2',
        'displacement_cc' => 'integer',
        'sort_order' => 'integer',
    ];

    public const CATEGORIES = [
        'ciclomotor' => 'Ciclomotor',
        'street' => 'Street',
        'scooter' => 'Scooter',
        'trail' => 'Trail',
        'custom' => 'Custom',
    ];

    protected static function booted(): void
    {
        static::saving(function (Moto $moto) {
            if (empty($moto->slug) && ! empty($moto->name)) {
                $moto->slug = Str::slug($moto->name);
            }
        });
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ '.number_format((float) $this->price, 2, ',', '.');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
