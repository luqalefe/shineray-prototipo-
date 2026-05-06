<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Support\PeriodFilter;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ConversionFunnelChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Funil de conversão';

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 1];

    protected ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $period = $this->filters['period'] ?? null;
        [$from, $to] = PeriodFilter::range($period);

        $base = Lead::query();
        if ($from) $base->where('created_at', '>=', $from);
        if ($to)   $base->where('created_at', '<', $to);

        $received = (clone $base)->count();
        $engaged = (clone $base)
            ->where(fn ($q) => $q->whereNotNull('installments')->orWhere('whatsapp_clicked', true))
            ->count();
        $attended = (clone $base)->whereNotIn('status', ['novo'])->count();
        $won = (clone $base)->where('status', 'fechado_ganho')->count();

        $stages = [
            ['Recebidos', $received, '#6b6b6b'],
            ['Engajaram', $engaged, '#7d0408'],
            ['Atendidos', $attended, '#C8080E'],
            ['Ganhos', $won, '#22c55e'],
        ];

        $labels = [];
        $data = [];
        $colors = [];
        foreach ($stages as [$name, $count, $color]) {
            $pct = $received > 0 ? round($count / $received * 100, 1) : 0;
            $labels[] = sprintf('%s · %d (%s%%)', $name, $count, number_format($pct, 1, ',', '.'));
            $data[] = $count;
            $colors[] = $color;
        }

        return [
            'datasets' => [[
                'label' => 'Leads',
                'data' => $data,
                'backgroundColor' => $colors,
                'borderRadius' => 4,
                'borderWidth' => 0,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0, 'stepSize' => 1],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y' => [
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['size' => 12]],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
