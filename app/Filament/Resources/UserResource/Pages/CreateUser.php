<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Criar novo usuário';
    protected ?string $subheading = 'Preencha todos os campos do formulário';
    protected ?string $maxContentWidth = '4xl';

    public function getCreatedNotificationTitle(): ?string
    {
        return 'Usuário criado com sucesso!';
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
