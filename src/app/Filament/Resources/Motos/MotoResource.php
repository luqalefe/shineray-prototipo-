<?php

namespace App\Filament\Resources\Motos;

use App\Filament\Resources\Motos\Pages\CreateMoto;
use App\Filament\Resources\Motos\Pages\EditMoto;
use App\Filament\Resources\Motos\Pages\ListMotos;
use App\Filament\Resources\Motos\Schemas\MotoForm;
use App\Filament\Resources\Motos\Tables\MotosTable;
use App\Models\Moto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MotoResource extends Resource
{
    protected static ?string $model = Moto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MotoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MotosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMotos::route('/'),
            'create' => CreateMoto::route('/create'),
            'edit' => EditMoto::route('/{record}/edit'),
        ];
    }
}
