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
                ->action('savePost')
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

    protected bool $isPublishAction;

    protected function getCreatedNotificationTitle(): string
    {
        return $this->isPublishAction ? 'Postagem publicada com sucesso!' : 'Postagem salva como rascunho';
    }

    public function savePost()
    {
        $this->isPublishAction = false;
        $this->create();
    }

    public function publishPost()
    {
        $this->isPublishAction = true;
        $this->create();
        $this->record->touch('published_at');
    }
}
