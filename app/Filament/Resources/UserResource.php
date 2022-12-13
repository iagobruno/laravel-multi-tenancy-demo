<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Resources\Form;
use Filament\Forms\Components\{Card, TextInput, Toggle, FileUpload};
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\{TextColumn, IconColumn};
use Filament\Tables\Actions\{Action, ActionGroup, ViewAction, EditAction, DeleteAction, DeleteBulkAction};
use Filament\Tables\Filters\{Filter};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
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
                    Toggle::make('is_admin')
                        ->label('Administrador:')
                        ->default(false)
                        ->inline(false),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(''),
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
                    ->description(fn (User $record) => $record->hasVerifiedEmail() ? 'Email verificado' : 'Email não verificado')
                    ->searchable(),
                TextColumn::make('age')
                    ->label('Idade')
                    ->sortable()
                    ->default('---')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_admin')
                    ->label('Administrador')
                    ->boolean(fn (User $record) => $record->isAdmin())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),
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
                    ViewAction::make()->label('Ver detalhes'),
                    EditAction::make()->label('Editar'),
                    DeleteAction::make()->label('Deletar'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
