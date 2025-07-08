# Test Ceisc - Plataforma Laravel

Este projeto é uma aplicação web baseada em Laravel, customizada para o desafio Ceisc.

## Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js e npm (para assets front-end)
- PostgreSQL (ou outro banco configurado no .env)
- Docker e Docker Compose (opcional, para rodar via container)

## Rodando Localmente

> **Atenção:** Para rodar localmente, é necessário:
> - Ter um banco de dados PostgreSQL em execução.
> - O banco deve estar vazio ou conter as tabelas criadas pelas migrations do Laravel.
> - Configure o arquivo `.env` conforme o exemplo abaixo:
>
> ```env
> DB_CONNECTION=pgsql
> DB_HOST=127.0.0.1
> DB_PORT=5432
> DB_DATABASE=nome_do_banco
> DB_USERNAME=usuario
> DB_PASSWORD=senha
> ```

1. Clone o repositório:
   ```bash
   git clone https://github.com/laercio-nogueira/ceisc_test.git
   cd test ceisc
   ```
2. Instale as dependências PHP:
   ```bash
   composer install
   ```
3. Instale as dependências JS:
   ```bash
   npm install
   ```
4. Copie o arquivo de ambiente:
   ```bash
   cp .env.example .env
   ```
5. Configure as variáveis do banco de dados no `.env`.
6. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```
7. Rode as migrations:
   ```bash
   php artisan migrate
   ```
8. Inicie o servidor de desenvolvimento:
   ```bash
   php artisan serve
   ```
   O projeto estará disponível em [http://localhost:8080](http://localhost:8000)

### Usuários de Login Padrão

Após rodar as migrations e seeders, você pode acessar o sistema com os seguintes usuários:

- **Usuário comum**
  - Email: test@example.com
  - Senha: password123

- **Usuário admin**
  - Email: admin@example.com
  - Senha: password123

### Cartão de Crédito de Teste (Stripe)

Para testar pagamentos, utilize o cartão de teste fornecido pela Stripe:

- **Número:** 4242 4242 4242 4242
- **Validade:** Qualquer data futura (ex: 12/34)
- **CVV:** Qualquer código de 3 dígitos (ex: 123)
- **CEP:** Qualquer valor

Mais informações: [Stripe Docs - Testing](https://docs.stripe.com/testing#cards)

## Rodando as Migrations

Com o banco configurado, execute:
```bash
php artisan migrate
```

## Rodando via Docker

1. Certifique-se de ter Docker e Docker Compose instalados.
2. Suba os containers:
   ```bash
   docker compose up --build
   ```
3. O serviço estará disponível em `http://localhost:8000` (ajuste conforme seu docker-compose.yml).

Para rodar as migrations dentro do container:
```bash
docker compose exec app php artisan migrate
```
(Substitua `app` pelo nome do serviço PHP no seu docker-compose.yml, se diferente)

## Testes Automatizados

Consulte o arquivo [`TESTS_README.md`](./TESTS_README.md) para instruções detalhadas sobre como rodar e escrever testes.

---

## Créditos

Este projeto utiliza o framework [Laravel](https://laravel.com).

O Laravel é um framework web com sintaxe expressiva e elegante. Veja mais na [documentação oficial](https://laravel.com/docs).

## Licença

MIT. Veja o arquivo LICENSE.
