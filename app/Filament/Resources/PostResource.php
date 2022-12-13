<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\{Post, User};
use Filament\Forms;
use Filament\Forms\Components\{Card, DateTimePicker, Grid, Placeholder, RichEditor, Select, TextInput};
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\{BadgeColumn, TextColumn, IconColumn};
use Filament\Tables\Filters\{Filter, SelectFilter, TernaryFilter};
use Filament\Tables\Actions\{EditAction, BulkAction, DeleteBulkAction};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'blog/posts';
    protected static ?string $modelLabel = 'Postagem';
    protected static ?string $pluralModelLabel = 'Postagens';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('title')
                        ->label('Título:')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', str()->slug($state)))
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->label('Slug:')
                        ->disabled()
                        ->required()
                        ->maxLength(255)
                        ->unique(Post::class, 'slug', fn ($record) => $record)
                        ->columnSpanFull(),
                    RichEditor::make('content')
                        ->label('')
                        ->required()
                        ->columnSpanFull(),
                ])
                    ->columns([
                        'sm' => 2,
                    ])
                    ->columnSpan(2),

                // Info card
                Card::make()->schema([
                    Placeholder::make('visilibity')
                        ->label('Visibilidade:')
                        ->content(function (?Post $record) {
                            if (is_null($record)) return '-';
                            if ($record->isScheduled) return 'Agendado';
                            if ($record->isPublished) return 'Público';
                            if (!$record->isPublished) return 'Rascunho';
                        }),
                    Placeholder::make('author.name')
                        ->label('Autor:')
                        ->content(fn (?Post $record) => $record?->author->name ?? '-'),
                    // DateTimePicker::make('published_at')
                    //     ->label('Publicar:')
                    //     ->placeholder('Imediatamente'),
                    Placeholder::make('published_at')
                        ->label('Data de publicação:')
                        ->visible(fn ($record) => $record && !is_null($record->published_at))
                        ->content(fn (?Post $record) => $record?->published_at?->format('M j, Y H:i:s') ?? '-'),
                    Placeholder::make('created_at')
                        ->label('Criado em:')
                        ->content(fn (?Post $record) => $record?->created_at->format('M j, Y H:i:s') ?? '-'),
                    Placeholder::make('updated_at')
                        ->label('Atualizado em:')
                        ->content(fn (?Post $record) => $record?->updated_at->format('M j, Y H:i:s') ?? '-'),
                    Placeholder::make('words_count')
                        ->label('Palavras:')
                        ->content(fn (?Post $record) => str($record?->content)->wordCount())
                        ->extraAttributes([
                            'x-init' => "
                                document.getElementById('data.content').addEventListener('keyup', debounce((evt) => {
                                    \$el.innerText = wordCount(evt.target.innerText);
                                }))
                                function wordCount(text) {
                                    return text.trim().split(/\w+/gim).length-1;
                                }
                                function debounce(func, timeout = 500){
                                    let timer;
                                    return (...args) => {
                                      clearTimeout(timer);
                                      timer = setTimeout(() => func.apply(this, args), timeout);
                                    };
                                }
                            "
                        ]),
                ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('author.name')
                    ->label('Autor')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Post $record) {
                        if ($record->isPublished) return 'Público';
                        if (!$record->isPublished) return 'Rascunho';
                        if ($record->isScheduled) return 'Agendado';
                    })
                    ->icon(function (Post $record) {
                        if ($record->isPublished) return 'heroicon-o-globe-alt';
                        if (!$record->isPublished) return 'heroicon-o-document-text';
                        if ($record->isScheduled) return 'heroicon-o-clock';
                    })
                    ->color(function (Post $record) {
                        if ($record->isPublished) return 'success';
                        if (!$record->isPublished) return 'secondary';
                        if ($record->isScheduled) return 'warning';
                    }),
                TextColumn::make('published_at')
                    ->label('Data de publicação')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'DESC')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status:')
                    ->options([
                        'published' => 'Publicados',
                        'scheduled' => 'Agendados',
                        'draft' => 'Rascunhos',
                    ])
                    ->query(function (Builder $query, $state) {
                        return match ($state['value']) {
                            'published' => $query->published(),
                            'scheduled' => $query->scheduled(),
                            'draft' => $query->draft(),
                            default => $query
                        };
                    }),
                SelectFilter::make('author')
                    ->label('Autor:')
                    ->relationship('author', 'name'),
            ])
            ->actions([
                EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                BulkAction::make('bulk-publish')
                    ->label('Publicar selecionados')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->action(fn (Collection $records) => $records->each->touch('published_at'))
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make()
                    ->label('Deletar selecionados'),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
