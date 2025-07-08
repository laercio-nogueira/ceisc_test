# Test Ceisc - Plataforma Laravel

Este projeto é uma aplicação web baseada em Laravel, customizada para o desafio Ceisc.

## Visão Geral do Projeto

Este projeto é uma plataforma de gestão de planos e usuários, com autenticação, painel administrativo, integração com Stripe, de assinaturas de um serviço de streaming, com funcionalidades para:
 - Cadastro e autenticação de usuários (com autenticação via OAuth 2.0).
 - Gestão de planos de assinatura (mensal, trimestral, anual), com ciclo de faturamento.
 - Integração com um gateway de pagamento (simular integração com Pagar.me).
 - Controle de acesso a conteúdos com base no plano do usuário.
 - Painel de administração para gerenciar usuários e planos.
 - Relatórios de receita por período.

## Checklist de Funcionalidades Obrigatórias

1. **Autenticação e Autorização**
  - [X] Cadastro de usuários com e-mail e senha
  - [X] Login com OAuth 2.0
  - [X] Autorização baseada em permissões (admin, usuário comum)
  - [X] Logout (incluindo revogação de tokens)
2. **Gestão de Assinaturas**
  - [X] CRUD de planos de assinatura (Mensal, Trimestral e Anual)
  - [ ] Possibilidade de desconto em pagamentos antecipados
  - [X] Cadastro, alteração e cancelamento de assinaturas
  - [ ] Controle de ciclo de pagamento e renovação automática
  - [ ] Notificação de expiração de assinatura (via e-mail)
3 . **Integração com Gateway de Pagamento**
  - [X] Simulação de integração com Pagar.me ou Stripe
  - [ ] Geração de cobrança automática
  - [ ] Cancelamento de cobrança
  - [ ] Notificação de pagamento confirmado
4. **Controle de Acesso a Conteúdos**
  - [X] Acesso aos conteúdos liberado conforme o plano ativo do usuário
  - [ ] Middleware para verificar assinatura ativa
5. **Painel de Administração**
  - [X] Gerenciamento de usuários e planos
  - [X] Visualização de métricas de receita e assinaturas

## Perfis de Usuário e Permissões

- **Admin:** acesso total ao painel administrativo, relatórios, gestão de planos e usuários.
- **Usuário comum:** acesso ao painel do usuário, visualização e contratação de planos.

## Variáveis de Ambiente Importantes

| Variável         | Descrição                        | Exemplo                |
|------------------|----------------------------------|------------------------|
| APP_URL          | URL base da aplicação            | http://localhost:8000  |
| DB_CONNECTION    | Driver do banco                  | pgsql                  |
| DB_HOST          | Host do banco                    | 127.0.0.1              |
| DB_PORT          | Porta do banco                   | 5432                   |
| DB_DATABASE      | Nome do banco                    | ceisc_db               |
| DB_USERNAME      | Usuário do banco                 | postgres               |
| DB_PASSWORD      | Senha do banco                   | postgres               |
| STRIPE_KEY       | Chave pública Stripe             | pk_test_xxx            |
| STRIPE_SECRET    | Chave secreta Stripe             | sk_test_xxx            |

## Como Popular o Banco

Para criar dados de exemplo (usuários, planos, etc):

```bash
php artisan migrate:fresh --seed
```

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
> 
 ```

1. Clone o repositório:
   ```bash
   git clone https://github.com/laercio-nogueira/ceisc_test.git
   cd test ceisc
   ```
2. Copie o arquivo de variáveis de ambiente:
   ```bash
   cp .env.example .env
   ```
3. Instale as dependências PHP:
   ```bash
   composer install
   ```
4. Instale as dependências JS:
   ```bash
   npm install
   ```
5. Configure as variáveis do banco de dados e outras no `.env`.
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

## Testes Automatizados

Consulte o arquivo [`TESTS_README.md`](./TESTS_README.md) para instruções detalhadas sobre como rodar e escrever testes.

---

## Créditos

Este projeto utiliza o framework [Laravel](https://laravel.com).

O Laravel é um framework web com sintaxe expressiva e elegante. Veja mais na [documentação oficial](https://laravel.com/docs).

## Licença

MIT. Veja o arquivo LICENSE.

## FAQ / Troubleshooting

- **Erro de permissão em storage:**  
  Rode `chmod -R 775 storage bootstrap/cache`
- **Erro de conexão com banco:**  
  Verifique as variáveis de ambiente e se o banco está rodando.
- **Problemas com Stripe:**  
  Confirme se está usando as chaves de teste e cartão de teste.

## Como Contribuir

1. Fork este repositório
2. Crie uma branch: `git checkout -b minha-feature`
3. Faça suas alterações e commit: `git commit -m 'Minha feature'`
4. Envie para o fork: `git push origin minha-feature`
5. Abra um Pull Request
