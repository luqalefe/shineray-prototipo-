<?php

namespace App\Filament\Resources\Salespeople\Tables;

use App\Models\Salesperson;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class SalespeopleTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->copyable()
                    ->toggleable(),
                IconColumn::make('active')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('leads_count')
                    ->label('Leads')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('last_assigned_at')
                    ->label('Último lead em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('active')->label('Status'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('resetCounter')
                    ->label('Resetar')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Salesperson $record) => "Resetar contador de {$record->name}?")
                    ->modalDescription('Zera leads_count e last_assigned_at apenas deste vendedor. Os outros mantêm o estado atual.')
                    ->action(function (Salesperson $record) {
                        $record->forceFill([
                            'last_assigned_at' => null,
                            'leads_count' => 0,
                        ])->save();

                        Notification::make()
                            ->success()
                            ->title("{$record->name} resetado")
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('resetCounters')
                        ->label('Resetar contadores')
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Resetar contadores dos selecionados?')
                        ->modalDescription('Zera leads_count e last_assigned_at dos vendedores marcados.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->forceFill([
                                    'last_assigned_at' => null,
                                    'leads_count' => 0,
                                ])->save();
                                $count++;
                            }

                            Notification::make()
                                ->success()
                                ->title("{$count} vendedores resetados")
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
