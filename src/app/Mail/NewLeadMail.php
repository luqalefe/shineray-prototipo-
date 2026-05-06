<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        $isSimulation = $this->lead->isSimulation();
        $motoLabel = $this->lead->moto?->name ?? 'sem moto específica';

        $subject = $isSimulation
            ? sprintf('[Simulação] %s — %s — %dx R$ %s',
                $this->lead->name,
                $motoLabel,
                (int) $this->lead->installments,
                number_format((float) $this->lead->installment_value, 2, ',', '.'),
            )
            : sprintf('[Novo lead] %s — %s', $this->lead->name, $motoLabel);

        $envelope = new Envelope(subject: $subject);

        if (! empty($this->lead->email)) {
            $envelope->replyTo = [new Address($this->lead->email, $this->lead->name)];
        }

        return $envelope;
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-lead');
    }

    public function attachments(): array
    {
        return [];
    }
}
