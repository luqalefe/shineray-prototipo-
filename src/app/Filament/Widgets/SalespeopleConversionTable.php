<?php

namespace App\Filament\Widgets;

use App\Models\Salesperson;
use App\Support\PeriodFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class SalespeopleConversionTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Conversão por vendedor';

    public function table(Table $table): Table
    {
        [$from, $to] = PeriodFilter::range($this->filters['period'] ?? null);
        $clip = function ($q) use ($from, $to) {
            if ($from) $q->where('created_at', '>=', $from);
            if ($to)   $q->where('created_at', '<', $to);
        };

        return $table
            ->query(
                Salesperson::query()->withCount([
                    'leads as leads_total' => $clip,
                    'leads as leads_open' => function ($q) use ($clip) {
                        $clip($q);
                        $q->whereIn('status', ['novo', 'em_atendimento']);
                    },
                    'leads as leads_won' => function ($q) use ($clip) {
                        $clip($q);
                        $q->where('status', 'fechado_ganho');
                    },
                    'leads as leads_lost' => function ($q) use ($clip) {
                        $clip($q);
                        $q->where('status', 'fechado_perdido');
                    },
                    'leads as leads_whatsapp' => function ($q) use ($clip) {
                        $clip($q);
                        $q->where('whatsapp_clicked', true);
                    },
                ]),
            )
            ->defaultSort('leads_won', 'desc')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label('Vendedor')
                    ->weight('bold')
                    ->description(fn (Salesperson $r) => $r->active ? null : 'inativo')
                    ->color(fn (Salesperson $r) => $r->active ? null : 'gray'),

                TextColumn::make('leads_total')
                    ->label('Leads')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('leads_open')
                    ->label('Em atendimento')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'warning' : 'gray'),

                TextColumn::make('leads_won')
                    ->label('Ganhos')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color('success'),

                TextColumn::make('leads_lost')
                    ->label('Perdidos')
                    ->numeric()
                    ->alignment('center')
                    ->badge()
                    ->color('danger')
                    ->toggleable(),

                TextColumn::make('conversion_rate')
                    ->label('Taxa de conversão')
                    ->alignment('center')
                    ->getStateUsing(function (Salesperson $r) {
                        $closed = (int) $r->leads_won + (int) $r->leads_lost;
                        return $closed > 0
                            ? number_format($r->leads_won / $closed * 100, 1, ',', '.').'%'
                            : '—';
                    })
                    ->badge()
                    ->color(function (Salesperson $r) {
                        $closed = (int) $r->leads_won + (int) $r->leads_lost;
                        if ($closed === 0) return 'gray';
                        $rate = $r->leads_won / $closed * 100;
                        return $rate >= 50 ? 'success' : ($rate >= 25 ? 'warning' : 'danger');
                    }),

                TextColumn::make('whatsapp_rate')
                    ->label('WhatsApp')
                    ->alignment('center')
                    ->getStateUsing(fn (Salesperson $r) => $r->leads_total > 0
                        ? number_format($r->leads_whatsapp / $r->leads_total * 100, 1, ',', '.').'%'
                        : '—',
                    )
                    ->color('gray'),

                TextColumn::make('last_assigned_at')
                    ->label('Último lead')
                    ->dateTime('d/m H:i')
                    ->placeholder('—')
                    ->toggleable(),
            ]);
    }
}
