<?php

namespace App\Support;

use Carbon\CarbonImmutable;

class PeriodFilter
{
    public const OPTIONS = [
        'today' => 'Hoje',
        '7d' => 'Últimos 7 dias',
        '30d' => 'Últimos 30 dias',
        '90d' => 'Últimos 90 dias',
        'this_month' => 'Este mês',
        'last_month' => 'Mês passado',
        'all' => 'Tudo',
    ];

    public const DEFAULT = '30d';

    /**
     * Devolve [from, to] para o período. `to` pode ser null = "agora".
     *
     * @return array{0: ?CarbonImmutable, 1: ?CarbonImmutable}
     */
    public static function range(?string $period): array
    {
        $now = CarbonImmutable::now();
        $period = $period ?: self::DEFAULT;

        return match ($period) {
            'today'      => [$now->startOfDay(), null],
            '7d'         => [$now->subDays(7), null],
            '30d'        => [$now->subDays(30), null],
            '90d'        => [$now->subDays(90), null],
            'this_month' => [$now->startOfMonth(), null],
            'last_month' => [$now->subMonthNoOverflow()->startOfMonth(), $now->startOfMonth()],
            'all', null  => [null, null],
            default      => [null, null],
        };
    }

    public static function label(?string $period): string
    {
        return self::OPTIONS[$period ?? self::DEFAULT] ?? self::OPTIONS[self::DEFAULT];
    }
}
