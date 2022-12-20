<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use App\Filament\Resources\CollectionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCollection extends EditRecord
{
    protected static string $resource = CollectionResource::class;

    protected static ?string $title = 'Editar coleção';
    protected ?string $maxContentWidth = '6xl';

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
