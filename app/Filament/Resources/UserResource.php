<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Forms\Components as FormComponents;
use Filament\Forms\Components\Card;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'users';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    FormComponents\TextInput::make('name')
                        ->label('Nome:')
                        ->required()
                        ->minLength(2)
                        ->maxLength(255)
                        ->placeholder('Digite seu nome completo'),
                    // ->disabled(!auth()->user()->isAdmin())
                    // ->extraAttributes(['title' => 'Text input']),
                    FormComponents\TextInput::make('age')
                        ->label('Idade:')
                        ->numeric()
                        ->minValue(16)
                        ->maxValue(60)
                        ->maxWidth('xs'),
                    FormComponents\TextInput::make('email')
                        ->label('Email:')
                        ->email()
                        ->required()
                        ->helperText('Ao digitar você aceita receber emails de marketing')
                        ->disabled(fn (Page $livewire) => $livewire instanceof EditUser),
                    FormComponents\TextInput::make('password')
                        ->label('Senha:')
                        ->password()
                        ->disableAutocomplete()
                        ->required()
                        ->hidden(fn (Page $livewire) => !$livewire instanceof CreateUser),
                    FormComponents\Toggle::make('is_admin')
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
                Tables\Columns\TextColumn::make('id')->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->limit(50)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->icon('heroicon-s-mail')
                    ->copyable()
                    ->tooltip('Clique para copiar')
                    ->description(fn (User $record) => $record->hasVerifiedEmail() ? 'Email verificado' : 'Email não verificado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Idade')
                    ->sortable()
                    ->default('---')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Administrador')
                    ->boolean(fn (User $record) => $record->isAdmin())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime() // Or ->since()
                    ->sortable()
                    ->toggleable()
                    ->tooltip(fn (User $record) => $record->created_at->format('d/m/Y H:m:s')),
            ])
            ->defaultSort('created_at', 'DESC')
            ->filters([
                Filter::make('Apenas administradores')
                    ->query(fn (Builder $query) => $query->where('email', 'LIKE', '%' . '@admin.com')),
                Filter::make('Criados hoje')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subHours(24)))
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Ver detalhes'),
                    Tables\Actions\EditAction::make()
                        ->label('Editar')
                        ->visible(fn (User $record): bool => true /*auth()->user()->can('update', $record)*/),
                    Tables\Actions\DeleteAction::make()
                        ->label('Deletar')
                        ->visible(fn (User $record): bool => true /*auth()->user()->can('update', $record)*/),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
