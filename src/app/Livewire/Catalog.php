<?php

namespace App\Livewire;

use App\Models\Moto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Catalog extends Component
{
    #[Url(as: 'cat', except: '')]
    public string $category = '';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function clearFilters(): void
    {
        $this->reset(['category', 'search']);
    }

    #[Title('Motos Shineray em Rio Branco — Catálogo')]
    public function render()
    {
        $featured = Moto::query()
            ->where('active', true)
            ->where('featured', true)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $motos = Moto::query()
            ->where('active', true)
            ->when($this->category !== '', fn ($q) => $q->where('category', $this->category))
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.catalog', [
            'featured' => $featured,
            'motos' => $motos,
            'categories' => Moto::CATEGORIES,
        ]);
    }
}
