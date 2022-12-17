<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $title = 'Criar novo cliente';
    protected ?string $subheading = 'Preencha todos os campos do formulário';
    protected ?string $maxContentWidth = '4xl';
    protected static bool $canCreateAnother = false;

    public function getCreatedNotificationTitle(): ?string
    {
        return 'Cliente criado com sucesso!';
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
