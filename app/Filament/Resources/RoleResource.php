<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Resources\RoleResource\Pages\EditRole;
use App\Filament\Resources\RoleResource\Pages\ListRoles;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationGroup = 'System Management';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->minLength(3)
                ->maxLength(255),
            Select::make('permission')
                ->multiple()
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => Permission::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                ->getOptionLabelsUsing(fn (array $values): array => Permission::whereIn('id', $values)->pluck('name', 'id')->toArray())
                ->relationship('permissions', 'name')
                ->preload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                ])
            ->filters([
                //
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
        ]);
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
