<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Pages\Actions\{Action, DeleteAction, RestoreAction};
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
                ->label('Visualizar')
                ->color('secondary')
                ->icon('heroicon-o-eye')
                ->url('#')
                ->hidden($this->record->trashed()),
            Action::make('save')
                ->label('Salvar alterações')
                // ->disabled(!$this->record->isDirty())
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-save')
                ->hidden($this->record->trashed()),
            Action::make('publish')
                ->label($this->record->isScheduled() ? 'Publicar agora' : 'Publicar')
                ->hidden($this->record->isPublished() || $this->record->trashed())
                ->action('publishPost')
                ->color('success')
                ->icon('heroicon-o-globe-alt')
                ->requiresConfirmation()
                ->modalSubheading('A postagem ficará visível para todos'),
            RestoreAction::make()
                ->label('Restaurar')
                ->icon('heroicon-o-reply')
                ->color('success')
                ->requiresConfirmation(false)
                ->successNotificationTitle('Postagem restaurada com sucesso!')
                ->visible($this->record->trashed()),
            DeleteAction::make()
                ->label('Mover para a lixeira')
                ->icon('heroicon-o-trash')
                ->modalHeading('Mover postagem para a lixeira?')
                ->modalSubheading('É possível desfazer essa ação posteriormente')
                ->successRedirectUrl($this->getResource()::getUrl('index'))
                ->successNotificationTitle('Postagem movida para a lixeira')
                ->visible($this->record->isPublished() && !$this->record->trashed()),
        ];
    }

    protected $notificationMessage = 'Alterações salvas';

    protected function getSavedNotificationTitle(): string
    {
        return $this->notificationMessage;
    }

    public function publishPost()
    {
        $this->notificationMessage = 'Postagem publicada com sucesso!';
        $this->record->published_at = now();
        $this->save();
    }
}
