<?php

namespace App\Filament\Resources\Motos\Pages;

use App\Filament\Resources\Motos\MotoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMotos extends ListRecords
{
    protected static string $resource = MotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
