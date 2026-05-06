<?php

namespace App\Filament\Resources\Salespeople\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalespersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do vendedor')
                ->columns(2)
                ->components([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Os leads atribuídos serão enviados para este e-mail.'),
                    TextInput::make('phone')
                        ->label('WhatsApp / Telefone')
                        ->tel()
                        ->maxLength(32),
                    Toggle::make('active')
                        ->label('Ativo no rodízio')
                        ->default(true)
                        ->helperText('Apenas vendedores ativos recebem leads.'),
                ]),
        ]);
    }
}
