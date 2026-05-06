<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Support\PeriodFilter;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LeadsPerDayChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Leads por dia';

    protected int|string|array $columnSpan = ['md' => 2, 'xl' => 1];

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $period = $this->filters['period'] ?? null;
        [$from, $to] = PeriodFilter::range($period);

        if ($from) {
            $start = CarbonImmutable::parse($from)->startOfDay();
        } else {
            $firstLead = Lead::min('created_at');
            $start = $firstLead
                ? CarbonImmutable::parse($firstLead)->startOfDay()
                : CarbonImmutable::now()->subDays(30)->startOfDay();
        }

        $end = $to
            ? CarbonImmutable::parse($to)->subSecond()
            : CarbonImmutable::now()->endOfDay();

        $rows = Lead::query()
            ->selectRaw('DATE(created_at) as bucket, count(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('bucket')
            ->pluck('total', 'bucket')
            ->toArray();

        // Preenche dias sem leads com zero pra manter sequência sem buracos.
        $data = [];
        $cursor = $start;
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-d');
            $data[$cursor->format('d/m')] = (int) ($rows[$key] ?? 0);
            $cursor = $cursor->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => array_values($data),
                    'backgroundColor' => '#C8080E',
                    'borderColor' => '#C8080E',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        // Sufixo plural — fica como markup default; Filament usa Chart.js
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => ['autoSkip' => true, 'maxRotation' => 0],
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0, 'stepSize' => 1],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
