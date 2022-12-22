<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Filament\Resources\CollectionResource\RelationManagers;
use App\Filament\Resources\CollectionResource\RelationManagers\ProductsRelationManager;
use App\Models\Collection;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Filament\Forms;
use Filament\Forms\Components\{Card, Textarea, TextInput};
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\{TextColumn};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $slug = 'shop/collections';
    protected static ?string $modelLabel = 'coleção';
    protected static ?string $pluralModelLabel = 'coleções';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormSchema());
    }

    public static function getFormSchema(): array
    {
        return [
            Card::make([
                TextInput::make('title')
                    ->label('Título:')
                    ->required()
                    ->maxLength(80)
                    ->lazy()
                    ->helperText(function ($state, $record) {
                        if ($record?->slug) $slug = $record->slug;
                        else $slug = $state ? SlugService::createSlug(Collection::class, 'slug', $state) : '...';
                        return tenant_route(tenant()->subdomain, 'collection_page', [$slug]);
                    }),
                Textarea::make('description')
                    ->label('Descrição:')
                    ->maxLength(255),
            ])
        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')
                ->limit(50)
                ->sortable()
                ->searchable(),
            TextColumn::make('slug')
                ->url(
                    fn ($record) => tenant_route(tenant()->subdomain, 'collection_page', [$record->slug])
                )
                ->openUrlInNewTab()
                ->icon('heroicon-o-external-link')
                ->limit(50),
            TextColumn::make('products_count')
                ->label('Produtos na coleção')
                ->getStateUsing(fn ($record) => $record->products()->count()),
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
            ->defaultSort('created_at', 'DESC')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record) => tenant_route(tenant()->subdomain, 'collection_page', [$record])),
                    DeleteAction::make()
                        ->modalSubheading('Essa ação é irreversível'),
                ])
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollections::route('/'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
