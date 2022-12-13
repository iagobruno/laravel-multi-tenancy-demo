<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions\{Action, DeleteAction};
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Editar usuário';
    protected ?string $maxContentWidth = '4xl';

    public function getTitle(): string
    {
        return "Editar usuário #" . $this->data['id'];
    }

    public function getActions(): array
    {
        return [
            Action::make('revoke-access')
                ->label('Revogar acesso')
                ->hidden(is_null($this->record->role) || $this->record->is(auth()->user()))
                ->action('revokeUserAccessToDashboard')
                ->requiresConfirmation()
                ->modalSubheading('Este usuário não terá mais acesso ao painel de controle.'),
            DeleteAction::make()->label('Deletar usuário'),
        ];
    }

    public function revokeUserAccessToDashboard()
    {
        $this->record->role = null;
        $this->record->save();
        $this->notify('success', 'O usuário não tem mais acesso ao painel de controle.');
    }

    public function getSavedNotificationTitle(): ?string
    {
        return 'Atualizado com sucesso!';
    }
}
