<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\Moto;
use App\Models\SimulatorSetting;
use App\Services\FinancingCalculator;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class FinancingSimulator extends Component
{
    public Moto $moto;
    public SimulatorSetting $settings;

    public ?float $downPayment = 0;
    public int $installments = 24;

    public string $customerName = '';
    public string $customerPhone = '';
    public string $customerEmail = '';

    public bool $simulationCompleted = false;
    public ?int $savedLeadId = null;

    public ?string $downPaymentNotice = null;

    public function mount(Moto $moto): void
    {
        $this->moto = $moto;
        $this->settings = SimulatorSetting::current();
        $this->downPayment = round((float) $moto->price * 0.20, 2);
        $this->installments = (int) min(
            $this->settings->max_installments,
            max($this->settings->min_installments, 24),
        );
    }

    #[Computed]
    public function minDownPayment(): float
    {
        return round((float) $this->moto->price * ((float) $this->settings->min_down_payment_percent / 100), 2);
    }

    #[Computed]
    public function maxDownPayment(): float
    {
        return round((float) $this->moto->price * ((float) $this->settings->max_down_payment_percent / 100), 2);
    }

    public function updatedDownPayment(mixed $value): void
    {
        $min = $this->minDownPayment;
        $max = $this->maxDownPayment;
        $fmt = fn (float $v) => 'R$ '.number_format($v, 2, ',', '.');

        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->downPayment = $min;
            $this->downPaymentNotice = 'Entrada mínima preenchida automaticamente: '.$fmt($min).'.';
            Log::info('Simulator downPayment empty — preenchido com mínimo', [
                'moto_id' => $this->moto->id,
                'min' => $min,
            ]);

            return;
        }

        $numeric = (float) $value;

        if ($numeric < $min) {
            $this->downPayment = $min;
            $this->downPaymentNotice = 'Valor abaixo do mínimo. Ajustamos para a entrada mínima de '.$fmt($min).'.';
            Log::info('Simulator downPayment abaixo do mínimo — ajustado', [
                'moto_id' => $this->moto->id,
                'attempted' => $numeric,
                'min' => $min,
            ]);

            return;
        }

        if ($numeric > $max) {
            $this->downPayment = $max;
            $this->downPaymentNotice = 'Valor acima do máximo. Ajustamos para a entrada máxima de '.$fmt($max).'.';
            Log::info('Simulator downPayment acima do máximo — ajustado', [
                'moto_id' => $this->moto->id,
                'attempted' => $numeric,
                'max' => $max,
            ]);

            return;
        }

        $this->downPaymentNotice = null;
    }

    #[Computed]
    public function financedAmount(): float
    {
        return max(0.0, (float) $this->moto->price - (float) ($this->downPayment ?? 0));
    }

    #[Computed]
    public function calculation(): array
    {
        return app(FinancingCalculator::class)->calculate(
            financedAmount: $this->financedAmount,
            monthlyRate: (float) $this->settings->default_interest_rate,
            installments: (int) $this->installments,
        );
    }

    protected function rules(): array
    {
        return [
            'downPayment' => [
                'required',
                'numeric',
                'min:'.$this->minDownPayment,
                'max:'.$this->maxDownPayment,
            ],
            'installments' => [
                'required',
                'integer',
                'min:'.$this->settings->min_installments,
                'max:'.$this->settings->max_installments,
            ],
            'customerName' => ['required', 'string', 'min:3', 'max:120'],
            'customerPhone' => ['required', 'string', 'min:8', 'max:32'],
            'customerEmail' => ['nullable', 'email', 'max:160'],
        ];
    }

    protected function messages(): array
    {
        return [
            'downPayment.min' => 'A entrada mínima é de R$ '.number_format($this->minDownPayment, 2, ',', '.').'.',
            'downPayment.max' => 'A entrada máxima é de R$ '.number_format($this->maxDownPayment, 2, ',', '.').'.',
            'installments.min' => 'Mínimo de '.$this->settings->min_installments.' parcelas.',
            'installments.max' => 'Máximo de '.$this->settings->max_installments.' parcelas.',
            'customerName.required' => 'Informe seu nome.',
            'customerPhone.required' => 'Informe seu WhatsApp com DDD.',
        ];
    }

    public function simulate(): void
    {
        $this->validate();
        $calc = $this->calculation;

        $lead = Lead::create([
            'name' => trim($this->customerName),
            'phone' => trim($this->customerPhone),
            'email' => $this->customerEmail !== '' ? trim($this->customerEmail) : null,
            'message' => null,
            'moto_id' => $this->moto->id,
            'source' => 'simulador',
            'status' => 'novo',
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 512),
            'vehicle_price' => $this->moto->price,
            'down_payment' => $this->downPayment,
            'financed_amount' => $this->financedAmount,
            'installments' => $this->installments,
            'interest_rate' => $this->settings->default_interest_rate,
            'installment_value' => $calc['installment_value'],
            'total_amount' => $calc['total_amount'],
        ]);

        $this->savedLeadId = $lead->id;
        $this->simulationCompleted = true;
    }

    public function trackWhatsappClick(): void
    {
        if ($this->savedLeadId) {
            Lead::whereKey($this->savedLeadId)->update(['whatsapp_clicked' => true]);
        }
    }

    public function resetSimulation(): void
    {
        $this->reset(['simulationCompleted', 'savedLeadId', 'customerName', 'customerPhone', 'customerEmail', 'downPaymentNotice']);
        $this->downPayment = round((float) $this->moto->price * 0.20, 2);
        $this->installments = 24;
    }

    #[Computed]
    public function whatsappLink(): string
    {
        if (! $this->savedLeadId) {
            return '#';
        }

        $price = number_format((float) $this->moto->price, 2, ',', '.');
        $down = number_format((float) $this->downPayment, 2, ',', '.');
        $installment = number_format((float) $this->calculation['installment_value'], 2, ',', '.');
        $total = number_format((float) $this->calculation['total_amount'] + (float) $this->downPayment, 2, ',', '.');

        $message = "Olá! Acabei de simular um financiamento no site da ".config('store.name').".\n\n"
            ."🏍️ *Modelo:* {$this->moto->name}\n"
            ."💰 *Valor da moto:* R$ {$price}\n"
            ."💵 *Entrada:* R$ {$down}\n"
            ."📅 *Parcelas:* {$this->installments}x de R$ {$installment}\n"
            ."🧮 *Total:* R$ {$total}\n\n"
            ."👤 *Meu nome:* {$this->customerName}\n"
            ."📱 *Telefone:* {$this->customerPhone}\n\n"
            ."Quero conversar sobre as condições e fechar negócio.";

        return whatsapp_link($message);
    }

    public function render()
    {
        return view('livewire.financing-simulator');
    }
}
