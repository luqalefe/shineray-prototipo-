<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Salesperson;
use Illuminate\Support\Facades\DB;

class LeadAssigner
{
    /**
     * Atribui o lead ao próximo vendedor (round-robin).
     *
     * Estratégia: pega o vendedor ativo cuja última atribuição é a mais antiga
     * (NULLs primeiro — vendedores novos têm prioridade). Usa lockForUpdate
     * dentro de transação para evitar race condition em criações simultâneas.
     */
    public function assign(Lead $lead): ?Salesperson
    {
        return DB::transaction(function () use ($lead) {
            $seller = Salesperson::query()
                ->where('active', true)
                ->orderByRaw('last_assigned_at IS NULL DESC')
                ->orderBy('last_assigned_at', 'asc')
                // Tiebreakers: timestamp tem resolução de 1s e múltiplos leads no
                // mesmo segundo deixariam o sort indefinido. leads_count garante
                // distribuição uniforme; id é determinismo total.
                ->orderBy('leads_count', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (! $seller) {
                return null;
            }

            $seller->forceFill([
                'last_assigned_at' => now(),
                'leads_count' => $seller->leads_count + 1,
            ])->save();

            $lead->forceFill(['salesperson_id' => $seller->id])->save();

            return $seller;
        });
    }
}
