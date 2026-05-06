<?php

namespace App\Filament\Pages;

use App\Support\PeriodFilter;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('period')
                ->label('Período')
                ->options(PeriodFilter::OPTIONS)
                ->default(PeriodFilter::DEFAULT)
                ->native(false)
                ->selectablePlaceholder(false),
        ]);
    }
}
