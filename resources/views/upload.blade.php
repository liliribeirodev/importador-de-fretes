<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Upload de Tabelas de Frete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

<div class="container">
    <h1>Upload de Tabelas de Frete (CSV)</h1>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('upload.salvar') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="cliente" class="form-label">Selecione o Cliente:</label>
            <select name="cliente_id" id="cliente" class="form-select" required>
                <option value="">Escolha um cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="arquivo" class="form-label">Escolha o arquivo CSV:</label>
            <input type="file" name="arquivos[]" id="arquivo" class="form-control" accept=".csv" multiple required>
        </div>

        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>

</body>
</html>