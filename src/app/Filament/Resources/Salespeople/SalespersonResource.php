<?php

namespace App\Filament\Resources\Salespeople;

use App\Filament\Resources\Salespeople\Pages\CreateSalesperson;
use App\Filament\Resources\Salespeople\Pages\EditSalesperson;
use App\Filament\Resources\Salespeople\Pages\ListSalespeople;
use App\Filament\Resources\Salespeople\Schemas\SalespersonForm;
use App\Filament\Resources\Salespeople\Tables\SalespeopleTable;
use App\Models\Salesperson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalespersonResource extends Resource
{
    protected static ?string $model = Salesperson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Vendedor';

    protected static ?string $pluralModelLabel = 'Vendedores';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return SalespersonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalespeopleTable::configure($table);
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
            'index' => ListSalespeople::route('/'),
            'create' => CreateSalesperson::route('/create'),
            'edit' => EditSalesperson::route('/{record}/edit'),
        ];
    }
}
