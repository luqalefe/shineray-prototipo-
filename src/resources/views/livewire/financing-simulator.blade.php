<div class="bg-white rounded-2xl shadow-md border border-ink-100 overflow-hidden">
@if(! $simulationCompleted)
    <div class="bg-ink-800 text-white px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-[0.25em] text-brand-500 font-bold">Simulador</p>
            <h3 class="font-display text-2xl font-bold uppercase leading-none">Simule seu financiamento</h3>
        </div>
        <div class="text-right">
            <p class="text-[10px] uppercase tracking-widest text-ink-300 font-semibold">Valor</p>
            <p class="font-display text-2xl font-bold text-brand-500 leading-none">R$ {{ number_format((float) $moto->price, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="p-6 space-y-6">
        <div>
            <div class="flex items-baseline justify-between mb-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600">Valor da entrada</label>
                <span class="text-xs text-ink-400">{{ number_format($this->minDownPayment / (float) $moto->price * 100, 0) }}% — {{ number_format($this->maxDownPayment / (float) $moto->price * 100, 0) }}%</span>
            </div>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-400 text-sm font-semibold">R$</span>
                <input type="number" step="100"
                       min="{{ $this->minDownPayment }}" max="{{ $this->maxDownPayment }}"
                       wire:model.blur="downPayment"
                       class="w-full pl-10 pr-3 py-2.5 border border-ink-200 rounded-md text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>
            <p class="text-xs text-ink-400 mt-1">Mínimo: R$ {{ number_format($this->minDownPayment, 2, ',', '.') }} — máximo: R$ {{ number_format($this->maxDownPayment, 2, ',', '.') }}</p>
            @if($downPaymentNotice)
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-2 py-1.5 mt-2">{{ $downPaymentNotice }}</p>
            @endif
            @error('downPayment') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-baseline justify-between mb-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600">Parcelas</label>
                <span class="font-display font-bold text-brand-500 text-lg leading-none">{{ $installments }}x</span>
            </div>
            <input type="range"
                   min="{{ $settings->min_installments }}" max="{{ $settings->max_installments }}"
                   step="{{ $settings->installments_step }}"
                   wire:model.live="installments"
                   class="w-full accent-brand-500">
            <div class="flex justify-between text-xs text-ink-400 mt-1">
                <span>{{ $settings->min_installments }}x</span>
                <span>{{ $settings->max_installments }}x</span>
            </div>
        </div>

        <div class="bg-ink-50 border border-ink-100 rounded-xl p-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-ink-400 font-semibold">Valor financiado</p>
                    <p class="font-display text-lg font-bold text-ink-800 leading-none mt-1">R$ {{ number_format($this->financedAmount, 2, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-widest text-ink-400 font-semibold">Em</p>
                    <p class="font-display text-lg font-bold text-ink-800 leading-none mt-1">{{ $installments }}x</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-ink-200 text-center">
                <div class="w-11 h-11 mx-auto bg-brand-500/10 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <p class="text-[10px] uppercase tracking-widest text-ink-400 font-semibold">Sua parcela</p>
                <p class="font-display text-3xl font-bold text-ink-300 leading-none mt-1 select-none tracking-wider">
                    {{ $installments }}x de R$ ••••,••
                </p>
                <p class="text-xs text-brand-500 mt-3 font-semibold">Preencha seus dados abaixo pra liberar a simulação</p>
                <p class="text-[11px] text-ink-400 mt-3">{{ $settings->disclaimer_text }}</p>
            </div>
        </div>

        <form wire:submit="simulate" class="space-y-4 pt-4 border-t border-ink-100">
            <h4 class="font-display text-lg font-bold uppercase text-ink-800">Pra ver sua parcela, deixe seus dados</h4>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <input type="text" wire:model.blur="customerName" placeholder="Seu nome completo *"
                           class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2.5 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    @error('customerName') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <input type="tel" wire:model.blur="customerPhone" placeholder="WhatsApp *"
                           inputmode="tel" maxlength="17" oninput="window.phoneMaskBR(this)"
                           class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2.5 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    @error('customerPhone') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <input type="email" wire:model.blur="customerEmail" placeholder="E-mail (opcional)"
                           class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2.5 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    @error('customerEmail') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="simulate"
                    class="w-full bg-brand-500 hover:bg-brand-600 disabled:opacity-60 text-white font-semibold py-3 rounded-md transition shadow-md shadow-brand-500/20">
                <span wire:loading.remove wire:target="simulate">Ver minha parcela e falar no WhatsApp</span>
                <span wire:loading wire:target="simulate" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="4"></circle>
                        <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                    </svg>
                    Calculando...
                </span>
            </button>
        </form>
    </div>
@else
    <div class="p-8 text-center space-y-5">
        <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <h3 class="font-display text-2xl font-bold uppercase text-ink-800">Simulação salva!</h3>
            <p class="text-ink-500 mt-1">Clique abaixo pra abrir o WhatsApp já com sua simulação pronta.</p>
        </div>

        <div class="bg-ink-50 rounded-xl border border-ink-100 p-4 text-left text-sm space-y-1.5">
            <p><span class="text-ink-400">Modelo:</span> <strong>{{ $moto->name }}</strong></p>
            <p><span class="text-ink-400">Entrada:</span> <strong>R$ {{ number_format((float) ($downPayment ?? 0), 2, ',', '.') }}</strong></p>
            <p><span class="text-ink-400">Parcelas:</span> <strong>{{ $installments }}x de R$ {{ number_format($this->calculation['installment_value'], 2, ',', '.') }}</strong></p>
            <p><span class="text-ink-400">Total:</span> <strong>R$ {{ number_format($this->calculation['total_amount'] + (float) $downPayment, 2, ',', '.') }}</strong></p>
        </div>

        <a href="{{ $this->whatsappLink }}" target="_blank" rel="noopener"
           wire:click="trackWhatsappClick"
           class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-md transition shadow-md shadow-green-500/20 inline-flex items-center justify-center gap-2">
            <x-icons.whatsapp class="w-5 h-5" />
            Falar no WhatsApp
        </a>

        <button type="button" wire:click="resetSimulation"
                class="text-sm text-ink-500 hover:text-brand-500 underline">
            Fazer nova simulação
        </button>
    </div>
@endif
</div>
