# Testello - Importador de fretes
Projeto Laravel para importação de tabelas de frete via CSV com Docker.

## Pré-requisitos
- Docker instalado no computador

## Passo a passo para rodar o projeto
1. **Clonar o repositório**
```bash
git clone https://github.com/liliribeirodev/importador-de-fretes.git
cd importador-de-fretes
```

2. **Criar o arquivo .env e rodar os containers**
- Rode no terminal na pasta do projeto:
```bash
cp .env.example .env
```
- Em seguida, suba os containers:
```bash
docker compose up -d
```

3. **Instalar dependências e preparar o ambiente**
- Entre no container PHP:
```bash
docker exec -it importador_app bash
```
- Dentro do container, rode:
```bash
composer install
php artisan key:generate
php artisan migrate
```

4. **Acessar o projeto no navegador**
- http://localhost:8000