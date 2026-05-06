<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salesperson extends Model
{
    protected $table = 'salespeople';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'active',
        'last_assigned_at',
        'leads_count',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_assigned_at' => 'datetime',
        'leads_count' => 'integer',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
