<?php

namespace App\Livewire;

use App\Models\Moto;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MotoDetail extends Component
{
    public Moto $moto;

    public function mount(Moto $moto): void
    {
        abort_unless($moto->active, 404);
        $this->moto = $moto;
    }

    public function render()
    {
        $related = Moto::query()
            ->where('active', true)
            ->where('id', '!=', $this->moto->id)
            ->where('category', $this->moto->category)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return view('livewire.moto-detail', [
            'related' => $related,
        ])->title($this->moto->name.' — '.config('store.name'));
    }
}
