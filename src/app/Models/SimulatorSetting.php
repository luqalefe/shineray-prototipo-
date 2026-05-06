<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulatorSetting extends Model
{
    protected $fillable = [
        'default_interest_rate',
        'min_installments',
        'max_installments',
        'installments_step',
        'min_down_payment_percent',
        'max_down_payment_percent',
        'disclaimer_text',
        'active',
    ];

    protected $casts = [
        'default_interest_rate' => 'decimal:4',
        'min_installments' => 'integer',
        'max_installments' => 'integer',
        'installments_step' => 'integer',
        'min_down_payment_percent' => 'decimal:2',
        'max_down_payment_percent' => 'decimal:2',
        'active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'default_interest_rate' => 0.0250,
            'min_installments' => 12,
            'max_installments' => 48,
            'installments_step' => 6,
            'min_down_payment_percent' => 10.00,
            'max_down_payment_percent' => 80.00,
            'disclaimer_text' => 'Valor estimado, sujeito a aprovação de crédito pela financeira.',
            'active' => true,
        ]);
    }
}
