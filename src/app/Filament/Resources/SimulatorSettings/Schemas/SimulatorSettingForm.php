<?php

namespace App\Filament\Resources\SimulatorSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SimulatorSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Taxa e parcelas')
                ->columns(2)
                ->components([
                    TextInput::make('default_interest_rate')
                        ->label('Taxa de juros mensal')
                        ->required()
                        ->numeric()
                        ->step(0.0001)
                        ->minValue(0)
                        ->maxValue(1)
                        ->suffix('a.m.')
                        ->helperText('Decimal: 0,0270 = 2,70% ao mês.'),
                    TextInput::make('installments_step')
                        ->label('Passo do slider')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->helperText('Pulo entre as opções de parcelas (ex.: 6 → 12, 18, 24...).'),
                    TextInput::make('min_installments')
                        ->label('Mínimo de parcelas')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->suffix('x'),
                    TextInput::make('max_installments')
                        ->label('Máximo de parcelas')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->suffix('x'),
                ]),

            Section::make('Entrada')
                ->columns(2)
                ->components([
                    TextInput::make('min_down_payment_percent')
                        ->label('Entrada mínima')
                        ->required()
                        ->numeric()
                        ->step(0.5)
                        ->minValue(0)
                        ->maxValue(99)
                        ->suffix('%'),
                    TextInput::make('max_down_payment_percent')
                        ->label('Entrada máxima')
                        ->required()
                        ->numeric()
                        ->step(0.5)
                        ->minValue(1)
                        ->maxValue(100)
                        ->suffix('%'),
                ]),

            Section::make('Texto e ativação')
                ->components([
                    Textarea::make('disclaimer_text')
                        ->label('Aviso legal exibido no simulador')
                        ->required()
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Toggle::make('active')
                        ->label('Simulador ativo no site'),
                ]),
        ]);
    }
}
