<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Support\PeriodFilter;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$from, $to] = PeriodFilter::range($this->filters['period'] ?? null);
        $periodLabel = PeriodFilter::label($this->filters['period'] ?? null);

        $base = Lead::query();
        if ($from) $base->where('created_at', '>=', $from);
        if ($to)   $base->where('created_at', '<', $to);

        $total = (clone $base)->count();
        $inProgress = (clone $base)->whereIn('status', ['novo', 'em_atendimento'])->count();
        $won = (clone $base)->where('status', 'fechado_ganho')->count();
        $lost = (clone $base)->where('status', 'fechado_perdido')->count();
        $whatsappClicked = (clone $base)->where('whatsapp_clicked', true)->count();

        $closed = $won + $lost;
        $conversionRate = $closed > 0 ? round($won / $closed * 100, 1) : 0.0;
        $whatsappRate = $total > 0 ? round($whatsappClicked / $total * 100, 1) : 0.0;

        return [
            Stat::make('Total de leads', number_format($total, 0, ',', '.'))
                ->description($periodLabel)
                ->descriptionIcon('heroicon-m-inbox')
                ->color('gray'),

            Stat::make('Em atendimento', number_format($inProgress, 0, ',', '.'))
                ->description($inProgress === 0 ? 'Nada pendente' : 'Status novo ou em atendimento')
                ->descriptionIcon('heroicon-m-clock')
                ->color($inProgress > 0 ? 'warning' : 'gray'),

            Stat::make('Vendas fechadas', number_format($won, 0, ',', '.'))
                ->description($lost > 0 ? "{$lost} perdidos · taxa {$conversionRate}%" : 'Negócios ganhos')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make('Cliques no WhatsApp', "{$whatsappRate}%")
                ->description("{$whatsappClicked} de {$total} leads abriram a conversa")
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color($whatsappRate >= 50 ? 'success' : ($whatsappRate >= 25 ? 'warning' : 'danger')),
        ];
    }
}
