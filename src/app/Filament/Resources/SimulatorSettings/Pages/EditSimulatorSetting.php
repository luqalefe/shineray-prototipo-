<?php

namespace App\Filament\Resources\SimulatorSettings\Pages;

use App\Filament\Resources\SimulatorSettings\SimulatorSettingResource;
use App\Models\SimulatorSetting;
use Filament\Resources\Pages\EditRecord;

class EditSimulatorSetting extends EditRecord
{
    protected static string $resource = SimulatorSettingResource::class;

    public function mount(int|string|null $record = null): void
    {
        parent::mount(SimulatorSetting::current()->getKey());
    }

    public function getBreadcrumb(): string
    {
        return 'Configurações';
    }

    public function getTitle(): string
    {
        return 'Configurações do Simulador';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getUrl();
    }
}
