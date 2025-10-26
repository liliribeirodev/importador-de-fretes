# Testello - Importador de fretes

Aplicação Laravel para importação de tabelas de frete via CSV. O projeto já inclui arquivos Docker para facilitar a execução local.

Pré-requisitos
- Docker instalado no computador

Passo-a-passo:
1. Clone o repositório e entre na pasta do projeto:

```bash
git clone https://github.com/liliribeirodev/importador-de-fretes.git
cd importador-de-fretes
```

2. Suba os containers:

```bash
docker compose up -d --build
```

3. Abra no navegador:

- http://localhost:8000

OBS:
- O entrypoint do container já automatiza: instalação de dependências (se necessário), geração de APP_KEY, execução de migrations/seed e criação do link de storage.
- Há um serviço `worker` no `docker-compose.yml` que processa filas em background.

Como o projeto lida com arquivos CSV grandes e timeouts HTTP
---------------------------------------------------

- Uploads são recebidos pelo servidor web, mas o processamento é feito em background por um job (fila). Isso evita timeouts HTTP porque a requisição retorna imediatamente ao usuário e o trabalho pesado acontece assincronamente.

Detalhes e mecanismos usados:

- Armazenamento em disco: o upload é movido para `storage/app/uploads` e só então é disparado o job `ProcessarCsvsJob`.

- Processamento em streaming: o job usa `fgetcsv()` para ler linha a linha (streaming), o que evita carregar o arquivo inteiro na memória.

- Processamento em lotes (batching): linhas são agrupadas em lotes (atualmente 1000 registros por upsert) e gravadas no banco com `upsert()`. Isso reduz número de queries e usa menos memória.

- Job em background / Queue worker: o `worker` do Docker executa `php artisan queue:work` e processa a fila separadamente do processo web, evitando qualquer timeout HTTP.

- Timeouts e retries: o job define um timeout grande (ex.: `public $timeout = 3600`) e o worker é executado com parâmetros apropriados (`--timeout=3600 --sleep=3 --tries=3`).



