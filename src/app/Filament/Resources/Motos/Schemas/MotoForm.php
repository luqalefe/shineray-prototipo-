<?php

namespace App\Filament\Resources\Motos\Schemas;

use App\Models\Moto;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->columns(2)
                ->components([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Usado na URL pública. Gerado automaticamente a partir do nome.'),
                    Select::make('category')
                        ->label('Categoria')
                        ->options(Moto::CATEGORIES)
                        ->required()
                        ->native(false),
                    TextInput::make('displacement_cc')
                        ->label('Cilindrada (cc)')
                        ->numeric()
                        ->suffix('cc'),
                    TextInput::make('price')
                        ->label('Preço')
                        ->required()
                        ->numeric()
                        ->prefix('R$')
                        ->step(0.01),
                    TextInput::make('sort_order')
                        ->label('Ordem')
                        ->numeric()
                        ->default(0)
                        ->helperText('Ordem de exibição no catálogo (menor primeiro).'),
                ]),

            Section::make('Conteúdo')
                ->components([
                    TextInput::make('short_description')
                        ->label('Descrição curta')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label('Descrição completa')
                        ->rows(4)
                        ->columnSpanFull(),
                    TagsInput::make('highlights')
                        ->label('Destaques')
                        ->helperText('Aperte Enter após cada item.')
                        ->columnSpanFull(),
                ]),

            Section::make('Imagens')
                ->columns(2)
                ->components([
                    FileUpload::make('image')
                        ->label('Imagem principal')
                        ->image()
                        ->directory('motos')
                        ->required(),
                    FileUpload::make('gallery')
                        ->label('Galeria')
                        ->image()
                        ->multiple()
                        ->directory('motos/gallery')
                        ->reorderable(),
                ]),

            Section::make('Publicação')
                ->columns(2)
                ->components([
                    Toggle::make('featured')
                        ->label('Destaque na home')
                        ->default(false),
                    Toggle::make('active')
                        ->label('Ativa no catálogo')
                        ->default(true),
                ]),
        ]);
    }
}
