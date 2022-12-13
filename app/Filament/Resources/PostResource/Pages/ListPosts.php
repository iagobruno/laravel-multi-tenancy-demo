<?php

namespace App\Filament\Resources\PostResource\Pages;

use Closure;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected ?string $subheading = 'Crie, edite e gerencie os posts em seu site.';

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nova postagem'),
        ];
    }

    protected function getTableRecordUrlUsing(): Closure
    {
        return function (Post $record) {
            if ($record->trashed()) return null;
            return static::getResource()::getUrl('edit', ['record' => $record]);
        };
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Nenhuma postagem encontrada';
    }
}
