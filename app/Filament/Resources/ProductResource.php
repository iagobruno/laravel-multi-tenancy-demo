<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\{Card, Placeholder, Textarea, TextInput};
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\{DeleteBulkAction, EditAction, RestoreAction};
use Filament\Tables\Columns\{BadgeColumn, ImageColumn, TextColumn};
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'shop/products';
    protected static ?string $modelLabel = 'Produto';
    protected static ?string $pluralModelLabel = 'Produtos';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('title')
                    ->label('Título:')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->disableAutocomplete()
                    ->placeholder('Camiseta manga curta')
                    ->columnSpanFull()
                    ->helperText(function ($state, $record) {
                        if ($record?->slug) $slug = $record->slug;
                        else $slug = $state ? SlugService::createSlug(Product::class, 'slug', $state) : '...';
                        return tenant_route(tenant()->subdomain, 'product_page', ['product' => $slug]);
                    }),
                Textarea::make('description')
                    ->label('Descrição:')
                    ->columnSpanFull(),
                // FIX: Aplicar uma máscara de formatação de dinheiro e salvar o valor como integer no banco de dados
                TextInput::make('price')
                    ->label('Preço:')
                    ->required()
                    ->numeric()
                    ->step(1)
                    // ->minValue(0)
                    // ->mask(
                    //     fn (TextInput\Mask $mask) => $mask
                    //         ->numeric()
                    //         ->decimalPlaces(2)
                    //         ->decimalSeparator(',')
                    //         ->thousandsSeparator('.')
                    //         ->padFractionalZeros()
                    //         ->normalizeZeros(false)
                    //         ->signed()
                    // )
                    ->prefix('R$')
                    ->maxWidth('sm')
                    // ->formatStateUsing(fn ($state) => ($state) / 100)
                    // ->dehydrateStateUsing(fn ($state) => dd($state))
                    ->disableAutocomplete()
                    ->columnSpan(1),
                TextInput::make('compare_at_price')
                    ->label('Comparação de preços:')
                    ->numeric()
                    ->step(1)
                    ->prefix('R$')
                    ->maxWidth('sm')
                    ->helperText('Para mostrar um preço reduzido, mova o valor original do produto para Comparação de preços. Insira um valor menor em Preço.')
                    ->disableAutocomplete()
                    ->columnSpan(1),
                TextInput::make('SKU')
                    ->label('SKU (Unidade de manutenção de estoque):')
                    ->maxLength(50)
                    ->maxWidth('sm')
                    ->helperText('Informação não exibida aos clientes')
                    ->disableAutocomplete(),
            ])
                ->columns([
                    'sm' => 2,
                ])
                ->columnSpan(2),

            // Info card
            Card::make()->schema([
                Placeholder::make('status')
                    ->label('Status:')
                    ->content(function (?Product $record) {
                        if (!$record) return '-';
                        if ($record->trashed()) return 'Arquivado';
                        return 'Ativo';
                    }),
                Placeholder::make('created_at')
                    ->label('Criado em:')
                    ->content(fn (?Product $record) => $record?->created_at->format('d/m/Y à\s H:i') ?? '-'),
                Placeholder::make('updated_at')
                    ->label('Atualizado em:')
                    ->content(fn (?Product $record) => $record?->updated_at->format('d/m/Y à\s H:i') ?? '-'),
            ])
                ->columnSpan(1)
        ])
            ->columns(3);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image')
                ->label('')
                ->getStateUsing(fn () => ''),
            TextColumn::make('title')
                ->label('Título')
                ->limit(50)
                ->searchable()
                ->sortable(),
            TextColumn::make('price')
                ->label('Preço')
                ->money('brl')
                ->sortable(),
            TextColumn::make('sku')
                ->label('SKU')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            BadgeColumn::make('status')
                ->label('Status')
                ->getStateUsing(function (Product $record) {
                    if ($record->trashed()) return 'Arquivado';
                    return 'Ativo';
                })
                ->icon(function (Product $record) {
                    if ($record->trashed()) return 'heroicon-o-archive';
                    return 'heroicon-o-check';
                })
                ->color(function (Product $record) {
                    if ($record->trashed()) return 'danger';
                    return 'success';
                }),
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
            ->filters([
                SelectFilter::make('status')
                    ->label('Status:')
                    ->options([
                        'arquived' => 'Arquivados',
                    ])
                    ->query(function (Builder $query, $state) {
                        return match ($state['value']) {
                            'arquived' => $query->onlyTrashed(),
                            default => $query->withoutTrashed(),
                        };
                    })
                    ->placeholder('Ativos'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->hidden(fn (Product $record) => $record->trashed()),
                RestoreAction::make()
                    ->label('Desarquivar')
                    ->visible(fn (Product $record) => $record->trashed())
                    ->requiresConfirmation(false),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Arquivar selecionados')
                    ->icon('heroicon-o-archive')
                    ->requiresConfirmation()
                    ->modalHeading('Arquivar produtos')
                    ->modalSubheading('Os produtos não poderão mais ser adicionados ao carinho pelos clientes. Você pode desfazer essa ação posteriormente.')
                    ->successNotificationTitle('Produtos foram arquivados'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
