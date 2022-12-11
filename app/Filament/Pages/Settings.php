<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components;
use App\Models\Settings as SettingsModel;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $slug = 'settings';
    protected static ?string $title = 'Configurações';
    protected ?string $subheading = 'Sub heading';
    protected static ?string $navigationIcon = 'heroicon-o-adjustments';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Configurações';
    protected static string $view = 'filament.pages.settings';
    protected ?string $maxContentWidth = '4xl';

    public ?string $site_name = null;
    public ?string $site_description = null;
    public ?string $store_segment = null;
    public ?string $main_email = null;
    public bool $marketing_consent = true;
    public ?string $primary_color = null;
    public ?string $subdomain = null;

    public function getFormSchema(): array
    {
        return [
            Components\Section::make('Informações da loja')
                ->schema([
                    Components\TextInput::make('site_name')
                        ->label('Nome da loja:')
                        ->required()
                        ->minLength(2)
                        ->maxLength(255),
                    Components\TextInput::make('site_description')
                        ->label('Breve descrição:')
                        ->maxLength(255)
                        ->helperText('Descrição da empresa geralmente usada nas biografias de redes sociais ou slogan.'),
                    Components\Select::make('store_segment')
                        ->label('Segmento da loja:')
                        ->options([
                            'beauty' => 'Beleza',
                            'clothing' => 'Vestuário',
                            'electronics' => 'Eletrônicos',
                            'furniture' => 'Móveis',
                            'handcrafts' => 'Artesanato',
                            'jewelry' => 'Jóias',
                            'painting' => 'Pintura',
                            'food' => 'Comida',
                            'sports' => 'Esportes',
                            'toys' => 'Brinquedos',
                            'services' => 'Serviços',
                            'virtual_services' => 'Serviços virtuais',
                            'other' => 'Outro',
                            'do_not_know' => 'Ainda não decidi',
                        ]),
                ]),

            Components\Section::make('Informações de contato')
                ->description('Usamos isso para entrar em contato com você')
                ->schema([
                    Components\TextInput::make('main_email')
                        ->label('E-mail de contato:')
                        ->email(),
                    Components\Toggle::make('marketing_consent')
                        ->label('Aceito receber e-mails de marketing.'),
                ]),

            Components\Section::make('Marca')
                ->schema([
                    Components\ColorPicker::make('primary_color')
                        ->label('Cor principal:')
                        ->helperText('As cores da marca que aparecem na loja, nas redes sociais e em outros lugares'),
                ]),

            Components\Section::make('Endereço')
                ->description('Usado em confirmações de pedido do cliente e na fatura.')
                ->schema([]),

            Components\Section::make('Domínios')
                ->schema([
                    Components\TextInput::make('subdomain')
                        ->label('Sub domínio da loja:')
                        ->minLength(4)
                        ->maxLength(50)
                        ->prefix('http://')
                        ->suffix(env('MAIN_DOMAIN')),
                ]),
        ];
    }

    public function mount(): void
    {
        $this->form->fill(
            SettingsModel::getAll()
        );
    }

    public function submit(): void
    {
        $this->validate();

        $updatedData = collect($this->form->getState())
            ->except(['subdomain'])
            ->whereNotNull()
            // ->dd() // Debug
            ->toArray();
        SettingsModel::set($updatedData);

        $this->notify('success', 'Configurações atualizadas!');
    }
}
