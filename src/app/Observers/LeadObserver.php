<?php

namespace App\Observers;

use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Services\LeadAssigner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadObserver
{
    public function __construct(private LeadAssigner $assigner) {}

    public function created(Lead $lead): void
    {
        // 1. Atribui ao próximo vendedor (se já não foi atribuído manualmente).
        if (! $lead->salesperson_id) {
            $this->assigner->assign($lead);
            $lead->refresh();
        }

        // 2. Decide o destinatário: vendedor designado > caixa comercial.
        $to = $lead->salesperson?->email ?: config('store.sales_email');

        if (empty($to)) {
            Log::warning('LeadObserver: nenhum destinatário disponível, e-mail não enviado.', ['lead_id' => $lead->id]);
            return;
        }

        try {
            Mail::to($to)->send(new NewLeadMail($lead->loadMissing(['moto', 'salesperson'])));
        } catch (\Throwable $e) {
            Log::error('LeadObserver: falha ao enviar e-mail de novo lead.', [
                'lead_id' => $lead->id,
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
