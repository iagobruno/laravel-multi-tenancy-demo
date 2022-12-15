<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions\{Action, DeleteAction, RestoreAction};
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Editar postagem';

    protected function getActions(): array
    {
        return [
            Action::make('view')
                ->label('Visualizar')
                ->icon('heroicon-o-eye')
                ->color('secondary')
                ->url(
                    tenant_route(tenant()->subdomain, 'product_page', ['product' => $this->record->slug])
                )
                ->openUrlInNewTab()
                ->hidden($this->record->trashed()),
            RestoreAction::make()
                ->label('Desarquivar')
                ->icon('heroicon-o-reply')
                ->color('success')
                ->visible($this->record->trashed())
                ->requiresConfirmation(false)
                ->successNotificationTitle('Produto desarquivado!'),
            DeleteAction::make()
                ->label('Arquivar')
                ->icon('heroicon-o-archive')
                ->requiresConfirmation()
                ->modalHeading('Arquivar produto')
                ->modalSubheading('O produto não poderá mais ser adicionado ao carinho pelos clientes. Você pode desfazer essa ação posteriormente.')
                ->successNotificationTitle('Produto arquivado!')
                ->successRedirectUrl(null),
        ];
    }
}
