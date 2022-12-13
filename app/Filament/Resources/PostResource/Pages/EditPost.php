<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Pages\Actions\{Action, DeleteAction};
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Editar postagem';
    protected ?string $maxContentWidth = '7xl';

    protected function getActions(): array
    {
        return [
            Action::make('view')
                ->label('Ver')
                ->color('secondary')
                ->icon('heroicon-o-eye')
                ->url('#'),
            Action::make('save')
                ->label('Salvar alterações')
                // ->disabled(!$this->record->isDirty())
                ->action('savePost')
                ->color('primary')
                ->icon('heroicon-o-save'),
            Action::make('publish')
                ->label('Publicar')
                ->hidden($this->record->isPublished)
                ->action('publishPost')
                ->color('success')
                ->icon('heroicon-o-globe-alt')
                ->requiresConfirmation()
                ->modalSubheading('A postagem ficará visível para todos'),
            DeleteAction::make()
                ->label('Deletar postagem')
                ->icon('heroicon-o-trash'),
        ];
    }

    protected bool $isPublishAction;

    protected function getSavedNotificationTitle(): string
    {
        return $this->isPublishAction ? 'Postagem publicada com sucesso!' : 'Alterações salvas';
    }

    public function savePost()
    {
        $this->isPublishAction = false;
        $this->save();
    }

    public function publishPost()
    {
        $this->isPublishAction = true;
        $this->record->published_at = now();
        $this->save();
    }
}
