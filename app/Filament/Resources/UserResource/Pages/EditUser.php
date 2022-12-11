<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
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
            Actions\DeleteAction::make()->label('Deletar usuário'),
        ];
    }

    public function getSavedNotificationTitle(): ?string
    {
        return 'Atualizado com sucesso!';
    }
}
