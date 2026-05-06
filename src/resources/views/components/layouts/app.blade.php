<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('store.name') }} — Catálogo oficial de motos Shineray em Rio Branco/AC. Atendimento via WhatsApp.">
    <title>{{ $title ?? config('store.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo-shineray.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=barlow-condensed:600,700,800,900|inter:400,500,600,700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              // Paleta oficial Shineray (extraída de shineray.com.br)
              brand: {
                50:  '#fdf2f2',
                100: '#fce4e4',
                200: '#fbb4b6',
                500: '#C8080E',  // vermelho primário Shineray
                600: '#a8060c',
                700: '#7d0408',
                800: '#5b0306',
              },
              ink: {
                900: '#1a1a1a',
                800: '#212121',  // antracite (texto SHINERAY na logo)
                700: '#2a2a2a',
                600: '#3a3a3a',
                500: '#474444',
                400: '#6b6b6b',
                300: '#b2b0b0',
                200: '#d0d0d0',
                100: '#eeeeee',
                50:  '#fafafa',
              },
            },
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
              display: ['"Barlow Condensed"', 'sans-serif'],
            }
          }
        }
      }
    </script>
    <script>
      // Máscara de telefone BR — (DD) NNNN-NNNN ou (DD) 9 NNNN-NNNN
      window.phoneMaskBR = function (el) {
        const d = el.value.replace(/\D/g, '').slice(0, 11);
        let f = '';
        if (d.length === 0)        f = '';
        else if (d.length <= 2)    f = '(' + d;
        else if (d.length <= 6)    f = '(' + d.slice(0, 2) + ') ' + d.slice(2);
        else if (d.length <= 10)   f = '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
        else                       f = '(' + d.slice(0, 2) + ') ' + d.slice(2, 3) + ' ' + d.slice(3, 7) + '-' + d.slice(7);
        el.value = f;
      };
    </script>
    @livewireStyles
</head>
<body class="bg-white text-ink-800 font-sans antialiased">

<header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-ink-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/logo-shineray.png') }}" alt="Shineray" class="h-8 w-auto">
            <span class="hidden sm:inline-block text-[10px] font-semibold uppercase tracking-[0.25em] text-ink-400 border-l border-ink-200 pl-3">Rio Branco · AC</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-ink-600">
            <a href="{{ route('home') }}" class="hover:text-brand-500 transition">Catálogo</a>
            <a href="{{ route('home') }}#contato" class="hover:text-brand-500 transition">Contato</a>
            <a href="{{ whatsapp_link('Olá, tenho interesse em uma moto Shineray.') }}" target="_blank" rel="noopener"
               class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-md inline-flex items-center gap-2 transition shadow-sm">
                <x-icons.whatsapp class="w-4 h-4" />
                WhatsApp
            </a>
        </nav>
        <a href="{{ whatsapp_link() }}" target="_blank" rel="noopener" class="md:hidden bg-brand-500 text-white px-3 py-2 rounded-md text-sm font-semibold">Falar agora</a>
    </div>
</header>

<main>
    {{ $slot }}
</main>

<footer id="contato" class="bg-ink-800 text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid md:grid-cols-3 gap-10">
        <div>
            <img src="{{ asset('img/logo-shineray.png') }}" alt="Shineray" class="h-10 w-auto brightness-0 invert">
            <p class="mt-4 text-ink-300 text-sm leading-relaxed">{{ config('store.name') }}<br>Concessionária autorizada Shineray no Acre.</p>
        </div>
        <div>
            <h4 class="font-display text-xl font-bold uppercase tracking-wide text-white">Onde estamos</h4>
            <p class="mt-3 text-ink-300 text-sm leading-relaxed">{{ config('store.address') }}</p>
        </div>
        <div>
            <h4 class="font-display text-xl font-bold uppercase tracking-wide text-white">Contato</h4>
            <ul class="mt-3 space-y-2 text-sm text-ink-200">
                <li>Telefone: <a class="hover:text-brand-500 transition" href="tel:+55{{ preg_replace('/\D/','',config('store.phone')) }}">{{ config('store.phone') }}</a></li>
                <li>WhatsApp: <a class="hover:text-brand-500 transition" target="_blank" href="{{ whatsapp_link() }}">Clique para conversar</a></li>
                <li>E-mail: <a class="hover:text-brand-500 transition" href="mailto:{{ config('store.email') }}">{{ config('store.email') }}</a></li>
                <li>Instagram: <a class="hover:text-brand-500 transition" target="_blank" href="https://instagram.com/{{ config('store.instagram') }}">@{{ config('store.instagram') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-ink-700 py-4 text-center text-xs text-ink-400">
        © {{ date('Y') }} {{ config('store.name') }} · Protótipo. Marca Shineray pertence à Shineray do Brasil.
    </div>
</footer>

<a href="{{ whatsapp_link('Olá, tenho interesse em uma moto Shineray.') }}" target="_blank" rel="noopener"
   class="fixed bottom-6 right-6 z-40 bg-green-500 hover:bg-green-600 text-white rounded-full p-4 shadow-xl shadow-green-500/30 transition-transform hover:scale-105"
   aria-label="Falar no WhatsApp">
    <x-icons.whatsapp class="w-7 h-7" />
</a>

@livewireScripts
</body>
</html>
