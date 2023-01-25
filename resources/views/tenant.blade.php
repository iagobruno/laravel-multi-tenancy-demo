@extends('base')

@section('page_title')
    {{ tenant()->name }}
@endsection

@section('body')
    <div class="mb-5 flex items-center justify-between">
        <div>
            <div class="text-base">Nome do site:</div>
            <h1 class="text-3xl font-semibold">{{ tenant()->name }}</h1>
        </div>

        <div class="flex items-center gap-2.5">
            @auth
                <img class="inline-block h-[28px] w-[28px] rounded-full"
                    src="{{ auth()->user()->avatar }}">
                <div>
                    <div class="text-xs text-gray-600">Logado como:</div>
                    <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                </div>
                <form action="{{ route('tenant.logout') }}" method="post">
                    @csrf
                    <button class="rounded bg-gray-300 px-1 text-sm text-gray-900">Sair</button>
                </form>
            @endauth
        </div>
    </div>

    <div class="flex w-full items-start gap-4">
        <section class="inline-block w-2/5 rounded-lg border border-gray-400 p-4 pt-3">
            <h4 class="text-2xl font-semibold">Usuários:</h4>
            <p class="text-sm text-gray-600">Cada subdominio deve ter uma lista separada de usuários!</p>

            <ol class="my-3 space-y-2">
                @foreach (\App\Models\User::all() as $user)
                    <li class="flex items-center gap-2">
                        <img class="h-[30px] w-[30px] rounded-full border border-gray-400/80"
                            src="{{ $user->avatar }}">
                        <span class="font-semibold">{{ $user->name }}</span>

                        @if (auth()->id() !== $user->id)
                            <form action="{{ route('tenant.login') }}" method="POST"
                                class="m-0 ml-auto">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit"
                                    class="inline-block cursor-pointer rounded border border-gray-600 px-1.5 py-0 text-xs">Fazer
                                    login
                                    como</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ol>

            <form action="{{ route('tenant.create_user') }}" method="post">
                @csrf
                <button type="submit" class="cursor-pointer rounded bg-blue-500 px-3 py-1 text-white">Criar novo
                    usuário</button>
            </form>
        </section>

        @auth
            <section class="inline-block w-3/5 rounded-lg border border-gray-400 p-4 pt-3">
                <h4 class="text-2xl font-semibold">Lista de tarefas:</h4>
                <p class="text-sm text-gray-600">Cada usuário tem sua própria lista de tarefas!</p>
            </section>
        @else
            <div class="my-10 box-border px-8 text-center text-base text-gray-600">Faça login para ver a lista de tarefas de
                algum usuário
            </div>
        @endauth
    </div>
@endsection
