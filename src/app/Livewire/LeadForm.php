<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\Moto;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LeadForm extends Component
{
    public ?Moto $moto = null;
    public string $source = 'site';

    #[Validate('required|string|min:3|max:120')]
    public string $name = '';

    #[Validate('required|string|min:8|max:32')]
    public string $phone = '';

    #[Validate('nullable|email|max:160')]
    public string $email = '';

    #[Validate('nullable|string|max:1000')]
    public string $message = '';

    #[Validate('accepted')]
    public bool $consent = false;

    public bool $sent = false;
    public ?string $whatsappUrl = null;

    public function mount(?Moto $moto = null, string $source = 'site'): void
    {
        $this->moto = $moto?->exists ? $moto : null;
        $this->source = in_array($source, array_keys(Lead::SOURCES), true) ? $source : 'site';

        if ($this->moto) {
            $this->message = "Tenho interesse na {$this->moto->name}.";
        }
    }

    public function rules(): array
    {
        return [
            'source' => ['required', Rule::in(array_keys(Lead::SOURCES))],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $lead = Lead::create([
            'name' => trim($this->name),
            'phone' => trim($this->phone),
            'email' => $this->email !== '' ? trim($this->email) : null,
            'message' => $this->message !== '' ? trim($this->message) : null,
            'moto_id' => $this->moto?->id,
            'source' => $this->source,
            'status' => 'novo',
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 512),
        ]);

        $this->whatsappUrl = $this->buildWhatsappUrl($lead);
        $this->sent = true;
    }

    public function reopen(): void
    {
        $this->reset(['name', 'phone', 'email', 'message', 'consent', 'sent', 'whatsappUrl']);
        if ($this->moto) {
            $this->message = "Tenho interesse na {$this->moto->name}.";
        }
    }

    private function buildWhatsappUrl(Lead $lead): string
    {
        $intro = $this->moto
            ? "Olá! Sou {$lead->name}. Tenho interesse na {$this->moto->name} ({$this->moto->formatted_price})."
            : "Olá! Sou {$lead->name}. Quero saber mais sobre as motos Shineray.";

        $extra = $this->message !== '' && $this->message !== ($this->moto ? "Tenho interesse na {$this->moto->name}." : '')
            ? "\n\n{$this->message}"
            : '';

        return whatsapp_link($intro.$extra);
    }

    public function render()
    {
        return view('livewire.lead-form');
    }
}
