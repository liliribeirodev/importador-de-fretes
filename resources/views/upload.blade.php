<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Testello - Upload de Tabelas de Frete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6fff2;
            color: #595959;
        }

        .container {
            margin-top: 2.5rem;
        }

        .form-label,
        label,
        h1,
        .alert,
        .form-select,
        .form-control {
            color: #595959 !important;
        }

        .btn-primary {
            background-color: #595959 !important;
            border-color: #595959 !important;
            color: #ffffff !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #333333 !important;
            border-color: #333333 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="p-5">

<div class="container">
    <div class="d-flex justify-content-center mt-3">
        <h1>Testello</h1>
    </div>
    <div class="d-flex justify-content-center mt-3">
        <h4>Upload de Tabelas de Frete (CSV)</h4>
    </div>
    @if(session('success') && !session('importacao_id'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if(session('importacao_id'))
        <div id="import-alert" class="alert alert-info mt-3">Arquivos enviados! O sistema está processando...</div>
        <script>
            (function(){
                const url = "{{ route('importacao.status', ['id' => session('importacao_id')]) }}";
                const el = document.getElementById('import-alert');
                let tries = 0;

                const interval = setInterval(async function(){
                    tries++;
                    try {
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) {
                            console.warn('A rota de status de importação retornou status falso', res.status);
                            return;
                        }

                        const data = await res.json();
                        console.log('Import status response:', data);

                        const status = (data.status || '').toString().toLowerCase();

                        if (status === 'done' || status === 'completed') {
                            el.className = 'alert alert-success mt-3';
                            el.textContent = 'Importação concluída com sucesso!';
                            clearInterval(interval);

                            try {
                                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
                                const completeUrl = "{{ route('importacao.complete', ['id' => session('importacao_id')]) }}";
                                const resp = await fetch(completeUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf
                                    },
                                    body: JSON.stringify({})
                                });

                                if (resp.ok) {
                                    window.location.reload();
                                } else {
                                    console.warn('Servidor não limpou importacao_id', resp.status);
                                }
                            } catch (err) {
                                console.error('Erro ao notificar servidor sobre conclusão:', err);
                            }
                        } else if (status === 'failed' || status === 'error') {
                            el.className = 'alert alert-danger mt-3';
                            el.textContent = 'Importação falhou.';
                            clearInterval(interval);
                        } else {
                            if (tries > 120) {
                                clearInterval(interval);
                                el.className = 'alert alert-warning mt-3';
                                el.textContent = 'Verificação de status expirou.';
                            }
                        }
                    } catch (err) {
                        console.error('Erro ao verificar status de importação:', err);
                    }
                }, 3000);
            })();
        </script>
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

        <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
    </form>
</div>

</body>
</html>