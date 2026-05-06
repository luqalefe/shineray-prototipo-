@php /** @var \App\Models\Moto $moto */ @endphp
<article class="group bg-white border border-ink-100 rounded-xl overflow-hidden hover:border-brand-500 hover:shadow-lg hover:shadow-brand-500/5 transition flex flex-col">
    <a href="{{ route('moto.show', $moto) }}" wire:navigate class="block aspect-[4/3] bg-gradient-to-br from-ink-50 to-ink-100 overflow-hidden">
        <img src="{{ asset('storage/'.$moto->image) }}" alt="{{ $moto->name }}"
             class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-500"
             loading="lazy">
    </a>
    <div class="p-4 flex flex-col gap-3 flex-1">
        <div class="flex items-center justify-between text-xs">
            <span class="bg-ink-100 text-ink-600 px-2 py-1 rounded uppercase tracking-wider font-semibold">{{ $moto->category_label }}</span>
            @if($moto->displacement_cc)
                <span class="text-ink-400 font-medium">{{ $moto->displacement_cc }}cc</span>
            @endif
        </div>
        <h3 class="font-display font-bold text-xl uppercase leading-tight text-ink-800">
            <a href="{{ route('moto.show', $moto) }}" wire:navigate class="hover:text-brand-500 transition">{{ $moto->name }}</a>
        </h3>
        <p class="text-sm text-ink-500 line-clamp-2 flex-1">{{ $moto->short_description }}</p>
        <div class="flex items-end justify-between pt-2 mt-auto">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-ink-400 font-semibold">A partir de</p>
                <p class="font-display text-2xl font-bold text-brand-500 leading-none">{{ $moto->formatted_price }}</p>
            </div>
            <a href="{{ whatsapp_link_for_moto($moto) }}" target="_blank" rel="noopener"
               class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-3 py-2 rounded-md transition inline-flex items-center gap-1">
                Tenho interesse
            </a>
        </div>
    </div>
</article>
