<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Criar categoria')
                ->mutateFormDataUsing(function (array $data): array {
                    return array_merge($data, [
                        'created_by' => auth()->id(),
                    ]);
                }),
        ];
    }
}
