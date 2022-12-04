<form action="{{ route('create-store') }}" method="post">
    @csrf
    @if ($errors->any())
        <div style="color:red">{{ $errors->first() }}</div>
    @endif
    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nome da loja">
    <input type="text" name="subdomain" value="{{ old('subdomain') }}" placeholder="Subdomínio">
    <button type="submit">Criar loja</button>
</form>
