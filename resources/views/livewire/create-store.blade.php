<div>
    <h1 class="mb-6 text-center text-3xl font-semibold">Criar nova loja virtual</h1>

    <form wire:submit.prevent="submit">
        {{ $this->form }}

        {{-- <button type="submit">
            Submit
        </button> --}}
    </form>

    {{ $this->modal }}
</div>
