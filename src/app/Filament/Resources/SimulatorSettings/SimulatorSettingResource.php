<?php

namespace App\Filament\Resources\SimulatorSettings;

use App\Filament\Resources\SimulatorSettings\Pages\EditSimulatorSetting;
use App\Filament\Resources\SimulatorSettings\Schemas\SimulatorSettingForm;
use App\Models\SimulatorSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SimulatorSettingResource extends Resource
{
    protected static ?string $model = SimulatorSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $modelLabel = 'Simulador';

    protected static ?string $pluralModelLabel = 'Simulador';

    protected static ?string $navigationLabel = 'Simulador';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return SimulatorSettingForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => EditSimulatorSetting::route('/'),
        ];
    }
}
