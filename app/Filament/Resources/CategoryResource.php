<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\{DeleteAction, EditAction, DeleteBulkAction};
use Filament\Tables\Columns\{TextColumn};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'blog/categories';
    protected static ?string $modelLabel = 'categoria';
    protected static ?string $pluralModelLabel = 'categorias';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormSchema());
    }

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome da categoria:')
                ->required()
                ->maxLength(255)
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', str()->slug($state))),
            TextInput::make('slug')
                ->label('Slug:')
                ->disabled()
                ->required()
                ->maxLength(255)
                ->unique(Category::class, 'slug', fn ($record) => $record),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug'),
                TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y à\s H:i')
                    ->size('sm')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modificado')
                    ->since()
                    ->size('sm')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('name', 'ASC')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
        ];
    }
}
