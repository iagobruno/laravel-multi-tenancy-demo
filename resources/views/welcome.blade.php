@extends('base')

@section('page_title')
    Lista de sites
@endsection

@section('body')
    <h1 class="mb-4 mt-8 text-4xl font-semibold">Lista de Sites:</h1>

    <ul class="my-4 list-inside list-disc space-y-1 text-xl">
        @foreach (\App\Models\Tenant::all() as $tenant)
            <li>
                <a
                    href="{{ route('tenant_app', ['tenant' => $tenant->subdomain]) }}"
                    class="cursor-pointer text-sky-600 hover:underline">{{ $tenant->name }}</a>
            </li>
        @endforeach
    </ul>

    <form action="{{ route('create_site') }}" method="post"
        class="inline-block rounded border border-gray-400 p-4 px-5">
        <div class="mb-1 text-lg font-semibold">Criar novo site</div>
        @csrf
        <div>
            <label for="name-field" class="block">Nome:</label>
            <input type="text" name="name" id="name-field"
                value="{{ old('name') }}"
                @class([
                    'block w-[360px] rounded border border border-gray-500 border-gray-500 px-2 py-1',
                    'border-rose-800' => $errors->has('name'),
                ])>
            @error('name')
                <div class="inline-block text-sm text-rose-800">{{ $message }}</div>
            @enderror
        </div>
        <div class="mt-1">
            <label for="subdomain-field" class="-mb-1 block">Sub-domínio:</label>
            <div class="mb-1 text-sm text-gray-600">Somente letras e números e sem espaço.</div>
            <input type="text" name="subdomain" id="subdomain-field"
                value="{{ old('subdomain') }}"
                @class([
                    'block w-[360px] rounded border border border-gray-500 border-gray-500 px-2 py-1',
                    'border-rose-800' => $errors->has('subdomain'),
                ])>
            @error('subdomain')
                <div class="inline-block text-sm text-rose-800">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="mt-3 cursor-pointer rounded bg-blue-500 px-3 py-1 text-white">Criar site</button>
    </form>
@endsection
