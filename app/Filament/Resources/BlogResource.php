<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\Pages\CreateBlog;
use App\Filament\Resources\BlogResource\Pages\EditBlog;
use App\Filament\Resources\BlogResource\Pages\ListBlogs;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Filament\Resources\BlogResource\RelationManagers\BlogRelationManager;
use App\Models\Blog;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Writer;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;



class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Blog Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('writer_id')
                    ->relationship('Writer', 'name')
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('intro')
                    ->required()
                    ->columnSpanFull()
                    ->autosize(),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->autosize(),
                Forms\Components\TextInput::make('photo')
                    ->required()
                    ->maxLength(255)
                    ->columns(3),
                Forms\Components\Select::make('tags')
                      ->label('Tags')
                      ->multiple()
                      ->searchable()
                      ->relationship(titleAttribute: 'name')
                      ->getSearchResultsUsing(fn (string $search): array => tag::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                      ->getOptionLabelsUsing(fn (array $values): array => tag::whereIn('id', $values)->pluck('name', 'id')->toArray())
                      ->preload(),
                    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('writer.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('intro')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('photo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tags.name')
                    ->searchable()
                    ->wrap()
                    ->badge()
                    ->label('Tags')
                    ->toggleable(isToggledHiddenByDefault: true),
                    ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                    ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
            ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() < 25 ? 'warning' : 'success';
    }

}
