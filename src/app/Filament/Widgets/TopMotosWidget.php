<?php

namespace App\Filament\Widgets;

use App\Models\Moto;
use App\Support\PeriodFilter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class TopMotosWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Motos mais simuladas';

    public function table(Table $table): Table
    {
        [$from, $to] = PeriodFilter::range($this->filters['period'] ?? null);
        $clip = function ($q) use ($from, $to) {
            if ($from) $q->where('created_at', '>=', $from);
            if ($to)   $q->where('created_at', '<', $to);
        };

        return $table
            ->query(
                Moto::query()
                    ->where('active', true)
                    ->withCount([
                        'leads as simulations_count' => function ($q) use ($clip) {
                            $clip($q);
                            $q->whereNotNull('installments');
                        },
                        'leads as total_leads' => $clip,
                        'leads as won_count' => function ($q) use ($clip) {
                            $clip($q);
                            $q->where('status', 'fechado_ganho');
                        },
                    ])
                    ->withAvg(
                        ['leads as avg_installment' => function ($q) use ($clip) {
                            $clip($q);
                            $q->whereNotNull('installment_value');
                        }],
                        'installment_value',
                    )
                    ->orderByDesc('simulations_count')
                    ->orderByDesc('total_leads'),
            )
            ->defaultPaginationPageOption(10)
            ->paginated([10])
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->height(40)
                    ->extraImgAttributes(['style' => 'object-fit:contain;background:#f5f5f5;border-radius:6px;padding:4px;']),

                TextColumn::make('name')
                    ->label('Moto')
                    ->weight('bold')
                    ->description(fn (Moto $r) => $r->category_label.($r->displacement_cc ? ' · '.$r->displacement_cc.'cc' : '')),

                TextColumn::make('formatted_price')
                    ->label('Preço'),

                TextColumn::make('simulations_count')
                    ->label('Simulações')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('total_leads')
                    ->label('Interessados')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color('gray')
                    ->tooltip('Total de leads no período (simulações + contatos diretos)'),

                TextColumn::make('won_count')
                    ->label('Vendidas')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'success' : 'gray')
                    ->toggleable(),

                TextColumn::make('avg_installment')
                    ->label('Parcela média')
                    ->alignment('right')
                    ->getStateUsing(fn (Moto $r) => $r->avg_installment
                        ? 'R$ '.number_format((float) $r->avg_installment, 2, ',', '.')
                        : '—',
                    ),
            ]);
    }
}
