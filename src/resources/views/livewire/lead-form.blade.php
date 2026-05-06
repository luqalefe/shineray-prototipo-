<div class="bg-white rounded-xl border border-ink-100 shadow-sm">
    @if($sent)
        <div class="p-6 sm:p-8 text-center">
            <div class="w-12 h-12 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="font-display text-2xl font-bold uppercase text-ink-800">Recebemos seu contato!</h3>
            <p class="mt-2 text-ink-500">Nosso time vai te chamar em instantes. Quer adiantar? É só abrir o WhatsApp:</p>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
               class="mt-5 inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-md transition shadow-md shadow-green-500/20">
                <x-icons.whatsapp class="w-5 h-5" />
                Abrir conversa no WhatsApp
            </a>
            <button type="button" wire:click="reopen" class="mt-4 block mx-auto text-sm text-ink-500 hover:text-brand-500 underline">
                Enviar outro contato
            </button>
        </div>
    @else
        <form wire:submit="submit" class="p-6 sm:p-8 space-y-4">
            <div>
                <h3 class="font-display text-2xl font-bold uppercase text-ink-800">
                    @if($moto)
                        Tenho interesse — {{ $moto->name }}
                    @else
                        Fale com a gente
                    @endif
                </h3>
                <p class="text-sm text-ink-500 mt-1">Preencha que retornamos pelo WhatsApp.</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">Nome *</label>
                    <input type="text" wire:model.blur="name" placeholder="Seu nome completo"
                           class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    @error('name') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">WhatsApp *</label>
                    <input type="tel" wire:model.blur="phone" placeholder="(68) 9 9999-9999" inputmode="tel"
                           maxlength="17"
                           oninput="window.phoneMaskBR(this)"
                           class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    @error('phone') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">E-mail</label>
                    <input type="email" wire:model.blur="email" placeholder="opcional"
                           class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    @error('email') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">Mensagem</label>
                    <textarea wire:model.blur="message" rows="3" placeholder="Conte o que está procurando, condição de pagamento, etc."
                              class="w-full bg-ink-50 border border-ink-200 rounded-md px-3 py-2 text-sm text-ink-800 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 resize-none"></textarea>
                    @error('message') <p class="text-xs text-brand-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-start gap-2 text-xs text-ink-500 cursor-pointer">
                <input type="checkbox" wire:model="consent" class="mt-0.5 accent-brand-500">
                <span>Autorizo o contato pela {{ config('store.name') }} para tratar deste interesse.</span>
            </label>
            @error('consent') <p class="text-xs text-brand-500 -mt-2">{{ $message }}</p> @enderror

            <button type="submit"
                    class="w-full bg-brand-500 hover:bg-brand-600 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold px-6 py-3 rounded-md transition shadow-md shadow-brand-500/20 inline-flex items-center justify-center gap-2"
                    wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">Enviar e abrir WhatsApp</span>
                <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="4"></circle>
                        <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                    </svg>
                    Enviando...
                </span>
            </button>

            <p class="text-[11px] text-ink-400 text-center">Ao enviar, você será redirecionado para o WhatsApp da loja.</p>
        </form>
    @endif
</div>
