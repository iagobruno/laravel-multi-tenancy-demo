<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Criar nova postagem';
    protected ?string $maxContentWidth = '7xl';
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge($data, [
            'author_id' => auth()->id(),
        ]);
    }

    protected function getActions(): array
    {
        return [
            Action::make('save_draft')
                ->label('Salvar rascunho')
                ->action('create')
                ->color('primary')
                ->icon('heroicon-o-save'),
            Action::make('publish')
                ->label('Publicar')
                ->action('publishPost')
                ->color('success')
                ->icon('heroicon-o-globe-alt')
                ->requiresConfirmation()
                ->modalSubheading('A postagem ficará visível para todos'),
        ];
    }

    protected string $notificationMessage = 'Postagem salva como rascunho';

    protected function getCreatedNotificationTitle(): string
    {
        return $this->notificationMessage;
    }

    public function publishPost()
    {
        $this->notificationMessage = 'Postagem publicada com sucesso!';
        $this->create();
        $this->record->touch('published_at');
    }
}
