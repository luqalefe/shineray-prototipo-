<?php

namespace App\Filament\Resources\Motos\Tables;

use App\Models\Moto;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MotosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->disk('public')
                    ->height(48),
                TextColumn::make('name')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Categoria')
                    ->formatStateUsing(fn (string $state) => Moto::CATEGORIES[$state] ?? $state)
                    ->badge()
                    ->sortable(),
                TextColumn::make('displacement_cc')
                    ->label('Cilindrada')
                    ->suffix(' cc')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),
                IconColumn::make('featured')
                    ->label('Destaque')
                    ->boolean(),
                IconColumn::make('active')
                    ->label('Ativa')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Ordem')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options(Moto::CATEGORIES),
                TernaryFilter::make('featured')->label('Destaque'),
                TernaryFilter::make('active')->label('Ativa'),
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
