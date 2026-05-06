<div>
    <section class="relative overflow-hidden bg-ink-800 text-white">
        <div class="absolute inset-0 opacity-30" style="background:
            radial-gradient(circle at 15% 50%, #C8080E 0%, transparent 35%),
            radial-gradient(circle at 85% 20%, #7d0408 0%, transparent 40%);"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <p class="font-display uppercase tracking-[0.3em] text-brand-500 text-sm font-bold">Concessionária autorizada · Acre</p>
            <h1 class="mt-3 font-display font-extrabold uppercase tracking-tight text-white text-5xl sm:text-6xl lg:text-7xl leading-none">
                Sua próxima moto<br>
                <span class="text-brand-500">está em Rio Branco.</span>
            </h1>
            <p class="mt-6 max-w-xl text-lg text-ink-200">
                Catálogo completo Shineray. Veja modelos, preços e fale agora mesmo com nosso time pelo WhatsApp.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#catalogo" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-6 py-3 rounded-md transition shadow-lg shadow-brand-500/20">Ver catálogo</a>
                <a href="{{ whatsapp_link('Olá! Quero conhecer as motos disponíveis.') }}" target="_blank" rel="noopener"
                   class="border border-white/30 hover:border-white text-white font-semibold px-6 py-3 rounded-md transition">
                    Falar no WhatsApp
                </a>
            </div>
        </div>
    </section>

    @if($featured->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="text-brand-500 font-display uppercase tracking-widest text-xs font-bold">Em destaque</p>
                <h2 class="font-display text-3xl font-bold uppercase text-ink-800">Modelos mais procurados</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featured as $moto)
                @include('livewire.partials.moto-card', ['moto' => $moto])
            @endforeach
        </div>
    </section>
    @endif

    <section id="catalogo" class="bg-ink-50 border-y border-ink-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-8">
                <div>
                    <p class="text-brand-500 font-display uppercase tracking-widest text-xs font-bold">Linha completa</p>
                    <h2 class="font-display text-3xl font-bold uppercase text-ink-800">Catálogo</h2>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar modelo..."
                           class="bg-white border border-ink-200 rounded-md px-4 py-2 text-sm text-ink-800 placeholder-ink-400 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 w-full sm:w-64">
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-8">
                <button wire:click="$set('category','')"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition
                               {{ $category === '' ? 'bg-brand-500 border-brand-500 text-white' : 'bg-white border-ink-200 text-ink-600 hover:border-brand-500 hover:text-brand-500' }}">
                    Todas
                </button>
                @foreach($categories as $value => $label)
                    <button wire:click="$set('category','{{ $value }}')"
                            class="px-4 py-2 rounded-full text-sm font-medium border transition
                                   {{ $category === $value ? 'bg-brand-500 border-brand-500 text-white' : 'bg-white border-ink-200 text-ink-600 hover:border-brand-500 hover:text-brand-500' }}">
                        {{ $label }}
                    </button>
                @endforeach
                @if($category !== '' || $search !== '')
                    <button wire:click="clearFilters" class="px-4 py-2 rounded-full text-sm text-ink-500 hover:text-brand-500 underline">Limpar filtros</button>
                @endif
            </div>

            @if($motos->isEmpty())
                <div class="text-center py-20 text-ink-500">
                    <p class="text-lg">Nenhuma moto encontrada com os filtros atuais.</p>
                    <button wire:click="clearFilters" class="mt-4 text-brand-500 hover:underline">Limpar filtros</button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($motos as $moto)
                        @include('livewire.partials.moto-card', ['moto' => $moto])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid lg:grid-cols-2 gap-10 items-start">
            <div>
                <p class="text-brand-500 font-display uppercase tracking-widest text-xs font-bold">Fale com a gente</p>
                <h2 class="mt-2 font-display text-4xl font-bold uppercase text-ink-800 leading-none">Não achou<br>o que procurava?</h2>
                <p class="mt-4 text-ink-500 max-w-md">Conte pra gente o que você precisa — moto pra trabalho, primeira moto, troca, financiamento. Nosso time monta a melhor proposta e te chama no WhatsApp.</p>
                <ul class="mt-6 space-y-3 text-sm text-ink-700">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Atendimento humano em Rio Branco
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simulação de financiamento sem compromisso
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Test ride na loja
                    </li>
                </ul>
            </div>
            <livewire:lead-form source="home" />
        </div>
    </section>
</div>
