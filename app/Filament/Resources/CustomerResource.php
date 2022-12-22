<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Resources\Form;
use Filament\Forms\Components\{Card, TextInput, Toggle, FileUpload, Radio};
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\{BadgeColumn, TextColumn, IconColumn};
use Filament\Tables\Actions\{Action, ActionGroup, ViewAction, EditAction, DeleteAction, DeleteBulkAction};
use Filament\Tables\Filters\{Filter};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\UserRoles;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $slug = 'customers';
    protected static ?string $modelLabel = 'cliente';
    protected static ?string $pluralModelLabel = 'clientes';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    FileUpload::make('avatar_url')
                        ->image()
                        ->avatar()
                        ->maxSize(5120)
                        ->directory('avatars'),
                    TextInput::make('name')
                        ->label('Nome:')
                        ->required()
                        ->minLength(2)
                        ->maxLength(255)
                        ->placeholder('Digite seu nome completo'),
                    // ->disabled(!auth()->user()->isAdmin())
                    // ->extraAttributes(['title' => 'Text input']),
                    TextInput::make('age')
                        ->label('Idade:')
                        ->numeric()
                        ->minValue(16)
                        ->maxValue(60)
                        ->maxWidth('xs'),
                    TextInput::make('email')
                        ->label('Email:')
                        ->email()
                        ->required()
                        ->helperText('Ao digitar você aceita receber emails de marketing')
                        ->disabledOn('edit'),
                    TextInput::make('password')
                        ->label('Senha:')
                        ->password()
                        ->disableAutocomplete()
                        ->required()
                        ->hiddenOn('edit'),
                    // Radio::make('role')
                    //     ->label('Função:')
                    //     ->options([
                    //         UserRoles::ADMIN->value => 'Administrador',
                    //         UserRoles::AUTHOR->value => 'Autor',
                    //     ])
                    //     ->descriptions([
                    //         UserRoles::ADMIN->value => 'Poder total sobre o site: pode editar produtos, modificar as configurações, etc.',
                    //         UserRoles::AUTHOR->value => 'Pode escrever e publicar seus próprios posts.',
                    //     ])
                    //     ->visible(auth()->user()->isAdmin()),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->disableLabel(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->icon('heroicon-s-mail')
                    ->copyable()
                    ->tooltip('Clique para copiar')
                    // ->description(fn (Customer $record) => $record->hasVerifiedEmail() ? 'Email verificado' : 'Email não verificado')
                    ->searchable(),
                TextColumn::make('age')
                    ->label('Idade')
                    ->sortable()
                    ->default('---')
                    ->toggleable(isToggledHiddenByDefault: true),
                // BadgeColumn::make('role')
                //     ->label('Função')
                //     ->enum([
                //         UserRoles::ADMIN->value => 'Administrador',
                //         UserRoles::AUTHOR->value => 'Autor',
                //     ])
                //     ->icon('heroicon-o-identification')
                //     ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime() // Or ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'DESC')
            ->filters([
                Filter::make('Apenas administradores')
                    ->query(
                        fn (Builder $query) => $query->onlyAdmins()
                    ),
                Filter::make('Criados hoje')
                    ->query(
                        fn (Builder $query) => $query->where('created_at', '>=', now()->subHours(24))
                    )
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
