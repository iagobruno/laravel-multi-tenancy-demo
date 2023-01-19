<?php

namespace App\Http\Livewire;

use App\Models\Store;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\{Select, Textarea, TextInput, Wizard};
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class CreateStore extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('Identidade')
                    ->icon('heroicon-o-home')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nome da loja:')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->disableAutocomplete(),
                        TextInput::make('subdomain')
                            ->label('Sub domínio:')
                            ->required()
                            ->minLength(4)
                            ->maxLength(50)
                            ->alphaDash()
                            ->disableAutocomplete()
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, Closure $fail) {
                                        $domainAlreadyTaken = DB::table('domains')
                                            ->where('domain', $value . '.' . env('MAIN_DOMAIN'))
                                            ->exists();
                                        if ($domainAlreadyTaken) {
                                            $fail("Este domínio já está em uso.");
                                        }
                                    };
                                },
                            ])
                            ->prefix('https://')
                            ->suffix(env('MAIN_DOMAIN')),
                    ]),
                Step::make('Detalhes')
                    ->icon('heroicon-o-identification')
                    ->schema([
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
                            ])
                            ->required(),
                        Textarea::make('site_description')
                            ->label('Breve descrição:')
                            ->maxLength(255)
                            ->rows(2)
                            ->helperText('Descrição da empresa geralmente usada nas biografias de redes sociais ou slogan.'),
                    ])
            ])
                // ->startOnStep(2)
                ->submitAction(new HtmlString('<button type="submit" class="bg-primary-600 px-3 py-1 text-white text-bold rounded-lg">Criar loja</button>')),
        ];
    }

    public function submit()
    {
        $formState = collect($this->form->getState());
        $data = $formState
            ->except(['subdomain'])
            ->whereNotNull()
            ->toArray();
        $subdomain = $formState->get('subdomain');

        DB::transaction(function () use ($data, $subdomain) {
            $tenant = Store::create([
                'owner_id' => auth()->id(),
                'settings' => $data,
            ]);
            $tenant->createDomain($subdomain . '.' . env('MAIN_DOMAIN'));
        });

        return redirect();
        // Mostrar mensagem "criando loja..."
        // Loja criada com sucesso
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function render(): View
    {
        return view('livewire.create-store')->layout('layouts.create-store-wrapper');
    }
}
