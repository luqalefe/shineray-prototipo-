<?php

namespace App\Filament\Resources\Salespeople\Pages;

use App\Filament\Resources\Salespeople\SalespersonResource;
use App\Models\Salesperson;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListSalespeople extends ListRecords
{
    protected static string $resource = SalespersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetRoundRobin')
                ->label('Resetar rodízio')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Resetar rodízio dos vendedores?')
                ->modalDescription('Zera o contador de leads e a marca de "última atribuição" de todos os vendedores ativos. O próximo lead recomeça pelo primeiro da fila.')
                ->modalSubmitActionLabel('Resetar')
                ->action(function () {
                    $count = Salesperson::query()
                        ->where('active', true)
                        ->update([
                            'last_assigned_at' => null,
                            'leads_count' => 0,
                        ]);

                    Notification::make()
                        ->success()
                        ->title('Rodízio resetado')
                        ->body($count === 1
                            ? '1 vendedor ativo foi zerado.'
                            : "{$count} vendedores ativos foram zerados.")
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
