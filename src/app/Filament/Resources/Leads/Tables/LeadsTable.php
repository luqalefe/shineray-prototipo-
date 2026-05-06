<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use App\Models\Moto;
use App\Models\Salesperson;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Recebido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->url(fn (Lead $r) => 'https://wa.me/'.preg_replace('/\D/', '', '55'.$r->phone), true),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('moto.name')
                    ->label('Moto')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('salesperson.name')
                    ->label('Vendedor')
                    ->placeholder('—')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Lead::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'novo' => 'danger',
                        'em_atendimento' => 'warning',
                        'fechado_ganho' => 'success',
                        'fechado_perdido' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('installments')
                    ->label('Simulação')
                    ->placeholder('—')
                    ->formatStateUsing(fn (?int $state, Lead $r) => $state
                        ? $state.'x R$ '.number_format((float) $r->installment_value, 2, ',', '.')
                        : null,
                    )
                    ->description(fn (Lead $r) => $r->installments
                        ? 'Entrada R$ '.number_format((float) $r->down_payment, 2, ',', '.')
                        : null,
                    )
                    ->toggleable(),
                IconColumn::make('whatsapp_clicked')
                    ->label('WA')
                    ->boolean()
                    ->tooltip('Cliente clicou no botão do WhatsApp')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source')
                    ->label('Origem')
                    ->formatStateUsing(fn (string $state) => Lead::SOURCES[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state) => $state === 'simulador' ? 'success' : 'gray')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Lead::STATUSES),
                SelectFilter::make('source')
                    ->label('Origem')
                    ->options(Lead::SOURCES),
                SelectFilter::make('moto_id')
                    ->label('Moto')
                    ->options(fn () => Moto::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('salesperson_id')
                    ->label('Vendedor')
                    ->options(fn () => Salesperson::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Filter::make('novos')
                    ->label('Apenas novos')
                    ->query(fn ($q) => $q->where('status', 'novo'))
                    ->toggle(),
                Filter::make('simulacoes')
                    ->label('Apenas simulações')
                    ->query(fn ($q) => $q->whereNotNull('installments'))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
