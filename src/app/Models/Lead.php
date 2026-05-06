<?php

namespace App\Models;

use App\Observers\LeadObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(LeadObserver::class)]
class Lead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'message',
        'moto_id',
        'salesperson_id',
        'source',
        'status',
        'notes',
        'ip',
        'user_agent',
        'vehicle_price',
        'down_payment',
        'financed_amount',
        'installments',
        'interest_rate',
        'installment_value',
        'total_amount',
        'whatsapp_clicked',
    ];

    protected $casts = [
        'vehicle_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'financed_amount' => 'decimal:2',
        'installments' => 'integer',
        'interest_rate' => 'decimal:4',
        'installment_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'whatsapp_clicked' => 'boolean',
    ];

    public const STATUSES = [
        'novo' => 'Novo',
        'em_atendimento' => 'Em atendimento',
        'fechado_ganho' => 'Fechado — ganho',
        'fechado_perdido' => 'Fechado — perdido',
    ];

    public const SOURCES = [
        'home' => 'Home',
        'produto' => 'Página do produto',
        'simulador' => 'Simulador de financiamento',
        'rodape' => 'Rodapé',
        'site' => 'Site',
    ];

    public function isSimulation(): bool
    {
        return $this->installments !== null && $this->installment_value !== null;
    }

    public function moto(): BelongsTo
    {
        return $this->belongsTo(Moto::class);
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(Salesperson::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
