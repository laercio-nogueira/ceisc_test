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
> STRIPE_KEY=pk_test_sua_chave_aqui
> STRIPE_SECRET=sk_test_sua_chave_aqui
> ```
>
> ⚠️ **As chaves STRIPE_KEY e STRIPE_SECRET não são fornecidas no repositório por segurança. Solicite as chaves ao responsável pelo projeto ou obtenha suas próprias chaves de teste no painel da Stripe. Após receber, preencha no arquivo `.env` local.**

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

## Rotas Web

| Método    | URI                        | Nome                        | Controller@Ação                        |
|-----------|----------------------------|-----------------------------|-----------------------------------------|
| GET/HEAD  | /                          | login                       | AuthController@showLoginForm            |
| POST      | /                          |                             | AuthController@login                    |
| GET/HEAD  | register                   | register                    | AuthController@showRegisterForm         |
| POST      | register                   |                             | AuthController@register                 |
| POST      | logout                     | logout                      | AuthController@logout                   |
| GET/HEAD  | dashboard                  | dashboard                   | HomeController@dashboard                |
| GET/HEAD  | admin                      | admin                       | AdminController@index                   |
| GET/HEAD  | admin/plans                | admin.plans.index           | AdminPlanController@index               |
| GET/HEAD  | admin/plans/{userPlan}     | admin.plans.show            | AdminPlanController@show                |
| PUT       | admin/plans/{userPlan}/status | admin.plans.update-status | AdminPlanController@updateStatus        |
| GET/HEAD  | admin/users/{user}/plans   | admin.plans.user-plans      | AdminPlanController@userPlans           |
| GET/HEAD  | payment                    | payment.form                | PaymentController@showPaymentForm       |
| POST      | payment/process            | payment.process             | PaymentController@processPayment        |
| GET/HEAD  | plans/my-plan              | plans.my-plan               | PlanController@myPlan                   |
| POST      | plans/assign               | plans.assign                | PlanController@assignPlan               |

> Para rotas de API, consulte a documentação Swagger em `/api/documentation`.

## Rodando as Migrations

Com o banco configurado, execute:
```bash
php artisan migrate
```

## Rodando via Docker

> **Atenção:** Antes de rodar o Docker Compose, certifique-se de copiar o arquivo `.env.example` para `.env` e preencher as chaves STRIPE_KEY e STRIPE_SECRET corretamente, conforme instruções acima.

1. Certifique-se de ter Docker e Docker Compose instalados.
2. Copie o arquivo de variáveis de ambiente:
   ```bash
   cp .env.example .env
   ```
3. Edite o arquivo `.env` e preencha as chaves STRIPE_KEY e STRIPE_SECRET.
4. Suba os containers:
   ```bash
   docker compose up --build
   ```
5. O serviço estará disponível em `http://localhost:8000` (ajuste conforme seu docker-compose.yml).

## Testes Automatizados

Consulte o arquivo [`TESTS_README.md`](./TESTS_README.md) para instruções detalhadas sobre como rodar e escrever testes.

## Documentação da API (Swagger)

Este projeto utiliza o [Swagger UI](https://swagger.io/tools/swagger-ui/) para documentar e testar a API.

**Como usar:**

1. Gere a documentação:
   ```bash
   php artisan l5-swagger:generate
   ```
2. Acesse a interface web:
   [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

As anotações OpenAPI devem ser feitas nos controllers. Veja exemplos em [DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger).

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
