<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\{Card, Checkbox, FileUpload, KeyValue, Placeholder, Repeater, Section, Select, Textarea, TextInput};
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
    protected static ?string $modelLabel = 'produto';
    protected static ?string $pluralModelLabel = 'produtos';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make([
                TextInput::make('title')
                    ->label('Título:')
                    ->required()
                    ->maxLength(255)
                    ->disableAutocomplete()
                    ->placeholder('Camiseta manga curta')
                    ->lazy()
                    ->helperText(function ($state, $record) {
                        // dd($record->toArray());
                        if ($record?->slug) $slug = $record->slug;
                        else $slug = $state ? SlugService::createSlug(Product::class, 'slug', $state) : '...';
                        return tenant_route(tenant()->subdomain, 'product_page', ['product' => $slug]);
                    })
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Descrição:')
                    ->columnSpanFull(),
                // FIX: Mostrar corretamente a imagem de produtos que já tenham imagem
                FileUpload::make('image_url')
                    ->label('Imagem do produto:')
                    ->image()
                    ->maxSize(1024)
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetHeight('1024')
                    ->imageResizeTargetWidth('1024')
                    ->imagePreviewHeight('300')
                    ->directory('products-images')
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU (Unidade de manutenção de estoque):')
                    ->maxLength(50)
                    ->helperText('Informação não exibida aos clientes')
                    ->disableAutocomplete()
                    ->columnSpan(1),
                TextInput::make('barcode')
                    ->label('Código de barras (ISBN, UPC, GTIN etc.):')
                    ->maxLength(255)
                    ->disableAutocomplete()
                    ->columnSpan(1),
                Select::make('collections')
                    ->label('Coleção:')
                    ->relationship('collections', 'title')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ])
                ->columns([
                    'sm' => 2,
                ])
                ->columnSpan(2),

            // Info card
            Card::make([
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
                ->columnSpan(1),

            Section::make('Preço')->schema([
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
                    ->placeholder('0,00')
                    ->maxWidth('sm')
                    // ->formatStateUsing(fn ($state) => ($state) / 100)
                    // ->dehydrateStateUsing(fn ($state) => dd($state))
                    ->disableAutocomplete(),
                TextInput::make('compare_at_price')
                    ->label('Comparação de preços:')
                    ->numeric()
                    ->step(1)
                    ->prefix('R$')
                    ->placeholder('0,00')
                    ->maxWidth('sm')
                    ->helperText('Para mostrar um preço reduzido, mova o valor original do produto para Comparação de preços. Insira um valor menor em Preço.')
                    ->disableAutocomplete(),
                TextInput::make('cost')
                    ->label('Custo por item:')
                    ->numeric()
                    ->step(1)
                    ->prefix('R$')
                    ->placeholder('0,00')
                    ->maxWidth('sm')
                    ->helperText('Informação não exibida aos clientes')
                    ->disableAutocomplete(),
            ])
                ->columns(2)
                ->columnSpan(2),

            Section::make('Variantes')->schema([
                Checkbox::make('has_variants')
                    ->label('Este produto tem opções, como tamanho ou cor')
                    ->default(false)
                    ->reactive(),

                Repeater::make('variants')
                    ->relationship('variants')
                    ->schema([
                        TextInput::make('name')
                            ->label('Variante')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Tamanho, Cor, Material, Estilo ...'),
                        TextInput::make('price')
                            ->label('Preço')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('0,00'),
                        TextInput::make('stock')
                            ->label('Quantidade')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->label('')
                    // ->itemLabel('ITEEEM')
                    ->createItemButtonLabel('Criar variante')
                    ->orderable()
                    ->columns(3)
                    ->visible(fn (callable $get) => $get('has_variants') === true),
            ])
                ->columnSpan(2),

            Section::make('Envio')->schema([
                Checkbox::make('shippable')
                    ->label('Este produto é físico')
                    ->default(true)
                    ->reactive(),
                Checkbox::make('returnable')
                    ->label('Este produto pode ser devolvido')
                    ->default(false)
                    ->visible(fn (callable $get) => $get('shippable') === true),
                Placeholder::make('shipping_explanation')
                    ->content('Os clientes não inserirão o endereço de entrega nem escolherão uma forma de frete ao comprar este produto.')
                    ->label('')
                    ->visible(fn (callable $get) => $get('shippable') === false)
            ])
                ->columnSpan(2),

            // Meta data
            Section::make('Meta dados')->schema([
                KeyValue::make('metadata')
                    ->label('')
                    // ->default([])
                    ->keyLabel('Chave')
                    ->keyPlaceholder('Nome da propriedade')
                    ->valueLabel('Valor')
                    ->valuePlaceholder('Valor da propriedade')
                    ->addButtonLabel('Novo')
                    ->reorderable(),
            ])
                ->columnSpan(2),
        ])
            ->columns(3);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getTableSchema()
    {
        return [
            ImageColumn::make('image_url')
                ->label('')
                ->size(50),
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
            TextColumn::make('updated_at')
                ->label('Modificado')
                ->since()
                ->size('sm')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y à\s H:i')
                ->size('sm')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableSchema())
            ->defaultSort('created_at', 'DESC')
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
