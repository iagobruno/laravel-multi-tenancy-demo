<?php

namespace App\Filament\Resources\CollectionResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\{AttachAction, DetachAction, DetachBulkAction, EditAction, ViewAction};
use Filament\Tables\Columns\{TextColumn};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(ProductResource::getTableSchema())
            ->defaultSort('created_at', 'DESC')
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()->label('Vincular um produto'),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn ($record) => ProductResource::getUrl('edit', [$record])),
                DetachAction::make(),
            ])
            ->bulkActions([
                DetachBulkAction::make(),
            ]);
    }
}
