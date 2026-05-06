<div>
<section class="bg-ink-50 border-b border-ink-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <a href="{{ route('home') }}" wire:navigate class="text-ink-500 hover:text-brand-500 text-sm inline-flex items-center gap-2 mb-8 transition">
            ← Voltar para o catálogo
        </a>

        <div class="grid lg:grid-cols-2 gap-10 items-center">
            <div class="bg-white rounded-2xl p-6 lg:p-10 border border-ink-100 shadow-sm">
                <img src="{{ asset('storage/'.$moto->image) }}" alt="{{ $moto->name }}"
                     class="w-full max-h-[480px] object-contain mx-auto">
            </div>
            <div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="bg-brand-50 text-brand-700 px-2 py-1 rounded uppercase tracking-wider font-bold">{{ $moto->category_label }}</span>
                    @if($moto->displacement_cc)
                        <span class="text-ink-500 font-medium">{{ $moto->displacement_cc }}cc</span>
                    @endif
                </div>
                <h1 class="mt-3 font-display font-extrabold uppercase text-5xl lg:text-6xl tracking-tight leading-none text-ink-800">{{ $moto->name }}</h1>
                <p class="mt-4 text-lg text-ink-600">{{ $moto->short_description }}</p>

                @if($moto->description)
                    <p class="mt-4 text-ink-500 leading-relaxed">{{ $moto->description }}</p>
                @endif

                @if(!empty($moto->highlights))
                    <ul class="mt-6 grid sm:grid-cols-2 gap-2">
                        @foreach($moto->highlights as $h)
                            <li class="flex items-start gap-2 text-sm text-ink-700">
                                <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ $h }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-8 p-5 bg-white border border-ink-100 rounded-xl shadow-sm">
                    <p class="text-xs uppercase tracking-widest text-ink-400 font-semibold">A partir de</p>
                    <p class="font-display text-4xl font-bold text-brand-500 leading-none mt-1">{{ $moto->formatted_price }}</p>
                    <div class="mt-4 grid sm:grid-cols-2 gap-2">
                        <a href="#simulador"
                           class="bg-brand-500 hover:bg-brand-600 text-white text-center font-semibold px-4 py-3 rounded-md transition shadow-md shadow-brand-500/20">
                            Simular financiamento
                        </a>
                        <a href="{{ whatsapp_link_for_moto($moto) }}" target="_blank" rel="noopener"
                           class="bg-green-500 hover:bg-green-600 text-white text-center font-semibold px-4 py-3 rounded-md transition inline-flex items-center justify-center gap-2 shadow-md shadow-green-500/20">
                            <x-icons.whatsapp class="w-5 h-5" />
                            WhatsApp
                        </a>
                    </div>
                    <p class="mt-3 text-xs text-ink-400 text-center">Atendimento humano · {{ config('store.phone') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="simulador" class="bg-ink-50 border-y border-ink-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-8">
            <p class="text-brand-500 font-display uppercase tracking-widest text-xs font-bold">Financiamento</p>
            <h2 class="font-display text-3xl font-bold uppercase text-ink-800">Calcule sua parcela</h2>
            <p class="mt-2 text-ink-500">Mexa nos valores e veja em tempo real. Ao enviar, abrimos o WhatsApp com sua simulação pronta.</p>
        </div>
        <livewire:financing-simulator :moto="$moto" :key="'sim-'.$moto->id" />
    </div>
</section>

@if($related->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="font-display text-2xl font-bold uppercase mb-6 text-ink-800">Outros modelos da categoria</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($related as $r)
            @include('livewire.partials.moto-card', ['moto' => $r])
        @endforeach
    </div>
</section>
@endif
</div>
