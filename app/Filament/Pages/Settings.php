<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\{Section, TextInput, Select, Toggle, ColorPicker};
use Illuminate\Support\Facades\Gate;

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

    public array $settings = [];

    protected function getFormStatePath(): string
    {
        return 'settings';
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Informações da loja')
                ->schema([
                    TextInput::make('site_name')
                        ->label('Nome da loja:')
                        ->required()
                        ->minLength(2)
                        ->maxLength(255),
                    TextInput::make('site_description')
                        ->label('Breve descrição:')
                        ->maxLength(255)
                        ->helperText('Descrição da empresa geralmente usada nas biografias de redes sociais ou slogan.'),
                    Select::make('store_segment')
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

            Section::make('Informações de contato')
                ->description('Usamos isso para entrar em contato com você')
                ->schema([
                    TextInput::make('main_email')
                        ->label('E-mail de contato:')
                        ->email(),
                    Toggle::make('marketing_consent')
                        ->label('Aceito receber e-mails de marketing.'),
                ]),

            Section::make('Marca')
                ->schema([
                    ColorPicker::make('primary_color')
                        ->label('Cor principal:')
                        ->helperText('As cores da marca que aparecem na loja, nas redes sociais e em outros lugares'),
                ]),

            Section::make('Endereço')
                ->description('Usado em confirmações de pedido do cliente e na fatura.')
                ->schema([]),

            Section::make('Domínios')
                ->schema([
                    TextInput::make('subdomain')
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
        Gate::authorize('manage-settings');

        // tenant()->settings = null;
        // tenant()->save();
        // dd(tenant()->settings);
        $this->form->fill(
            tenant()->settings ?? []
        );
    }

    public function submit(): void
    {
        Gate::authorize('manage-settings');
        $this->validate();

        $updatedData = collect($this->form->getState())
            ->except(['subdomain'])
            ->whereNotNull()
            // ->dd() // Debug
            ->toArray();
        tenant()->updateSettings($updatedData);

        $this->notify('success', 'Configurações atualizadas!');
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return Gate::check('manage-settings');
    }
}
