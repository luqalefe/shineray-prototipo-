<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use App\Models\Moto;
use App\Models\Salesperson;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do contato')
                ->columns(2)
                ->components([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('phone')
                        ->label('WhatsApp / Telefone')
                        ->required()
                        ->tel()
                        ->maxLength(32),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->maxLength(160),
                    Select::make('moto_id')
                        ->label('Moto de interesse')
                        ->options(fn () => Moto::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('Sem moto específica'),
                    Textarea::make('message')
                        ->label('Mensagem do cliente')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Simulação de financiamento')
                ->columns(3)
                ->collapsed(fn ($record) => ! ($record && $record->installments))
                ->visible(fn ($record) => $record && $record->installments !== null)
                ->components([
                    TextInput::make('vehicle_price')
                        ->label('Valor da moto')
                        ->prefix('R$')
                        ->disabled(),
                    TextInput::make('down_payment')
                        ->label('Entrada')
                        ->prefix('R$')
                        ->disabled(),
                    TextInput::make('financed_amount')
                        ->label('Financiado')
                        ->prefix('R$')
                        ->disabled(),
                    TextInput::make('installments')
                        ->label('Parcelas')
                        ->suffix('x')
                        ->disabled(),
                    TextInput::make('installment_value')
                        ->label('Valor da parcela')
                        ->prefix('R$')
                        ->disabled(),
                    TextInput::make('total_amount')
                        ->label('Total')
                        ->prefix('R$')
                        ->disabled(),
                    TextInput::make('interest_rate')
                        ->label('Taxa (a.m.)')
                        ->disabled()
                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 2, ',', '.').'%' : null),
                ]),

            Section::make('Atendimento')
                ->columns(2)
                ->components([
                    Select::make('salesperson_id')
                        ->label('Vendedor responsável')
                        ->options(fn () => Salesperson::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('— sem vendedor —')
                        ->helperText('Para reatribuir, escolha outro vendedor e salve.'),
                    Select::make('status')
                        ->label('Status')
                        ->options(Lead::STATUSES)
                        ->required()
                        ->default('novo')
                        ->native(false),
                    Select::make('source')
                        ->label('Origem')
                        ->options(Lead::SOURCES)
                        ->required()
                        ->default('site')
                        ->native(false),
                    Textarea::make('notes')
                        ->label('Notas internas')
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Visível apenas no admin.'),
                ]),
        ]);
    }
}
