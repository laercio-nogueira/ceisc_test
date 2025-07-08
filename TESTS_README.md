# Testes Unitários e de Feature

Este projeto possui uma suíte completa de testes unitários e de feature para garantir a qualidade e funcionalidade do código.

## Estrutura dos Testes

### Testes Unitários (`tests/Unit/`)

1. **UserTest.php** - Testa o modelo User
   - Criação de usuários
   - Relacionamentos com UserPlan
   - Hash de senhas
   - Validação de email único
   - Roles de usuário (user/admin)
   - Relacionamento com planos ativos

2. **PlanTest.php** - Testa o modelo Plan
   - Criação de planos
   - Relacionamentos com UserPlan
   - Codificação JSON de features
   - Validação de preços
   - Validação de campos obrigatórios

3. **UserPlanTest.php** - Testa o modelo UserPlan
   - Criação de planos de usuário
   - Relacionamentos com User e Plan
   - Status padrão (active)
   - Validação de períodos de cobrança
   - Campos opcionais (expires_at, notes)

4. **StripePaymentControllerTest.php** - Testa o controller de pagamentos Stripe
   - Criação de PaymentIntent
   - Tratamento de erros
   - Conversão de valores para centavos
   - Logs de sucesso e erro
   - Mocks do Stripe

### Testes de Feature (`tests/Feature/`)

1. **AuthControllerTest.php** - Testa autenticação
   - Formulários de login e registro
   - Registro de usuários
   - Login com credenciais válidas/inválidas
   - Logout
   - Validação de dados
   - Roles padrão

2. **PaymentControllerTest.php** - Testa processamento de pagamentos
   - Formulário de pagamento
   - Cálculo de valores por período
   - Processamento de pagamentos
   - Restrições para admins
   - Desativação de planos anteriores
   - Criação de datas de expiração

3. **AdminControllerTest.php** - Testa funcionalidades admin
   - Acesso ao dashboard admin
   - Restrições de acesso
   - Estatísticas do dashboard
   - Planos recentes de usuários
   - Estatísticas por plano

4. **RoutesTest.php** - Testa rotas da aplicação
   - Acesso a rotas protegidas
   - Redirecionamentos
   - Middleware de autenticação
   - Middleware de roles
   - Proteção CSRF

## Factories

### UserFactory.php
- Criação de usuários com dados faker
- Estados: `admin()`, `user()`
- Senha padrão: `password`

### PlanFactory.php
- Criação de planos com preços aleatórios
- Estados: `basic()`, `premium()`, `enterprise()`
- Features em JSON

### UserPlanFactory.php
- Criação de planos de usuário
- Estados: `active()`, `inactive()`, `expired()`
- Períodos: `monthly()`, `semiannual()`, `annual()`
- Estado `permanent()` para planos sem expiração

## Como Executar os Testes

### Executar todos os testes
```bash
php artisan test
```

### Executar apenas testes unitários
```bash
php artisan test --testsuite=Unit
```

### Executar apenas testes de feature
```bash
php artisan test --testsuite=Feature
```

### Executar um arquivo específico
```bash
php artisan test tests/Unit/UserTest.php
```

### Executar um método específico
```bash
php artisan test --filter test_user_can_be_created
```

### Executar com cobertura de código (requer Xdebug)
```bash
php artisan test --coverage
```

### Executar testes em paralelo
```bash
php artisan test --parallel
```

## Configuração

### Ambiente de Teste
- Banco de dados: SQLite em memória
- Cache: Array
- Sessão: Array
- Mail: Array
- Stripe: Chaves de teste mockadas

### Variáveis de Ambiente para Teste
```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
STRIPE_KEY=pk_test_test
STRIPE_SECRET=sk_test_test
```

## Boas Práticas Implementadas

1. **RefreshDatabase** - Cada teste usa um banco limpo
2. **Factories** - Criação de dados de teste consistentes
3. **Mocks** - Isolamento de dependências externas (Stripe)
4. **Assertions** - Verificações específicas e claras
5. **Nomenclatura** - Nomes descritivos para métodos de teste
6. **Organização** - Separação clara entre testes unitários e de feature

## Cobertura de Testes

Os testes cobrem:
- ✅ Modelos (User, Plan, UserPlan)
- ✅ Controllers (Auth, Payment, Admin, Stripe)
- ✅ Rotas e middleware
- ✅ Relacionamentos entre modelos
- ✅ Validações e regras de negócio
- ✅ Autenticação e autorização
- ✅ Processamento de pagamentos
- ✅ Funcionalidades administrativas

## Adicionando Novos Testes

1. Crie o arquivo na pasta apropriada (`Unit/` ou `Feature/`)
2. Use as factories existentes para criar dados
3. Siga o padrão de nomenclatura: `test_should_do_something`
4. Use assertions específicas e claras
5. Mock dependências externas quando necessário
6. Documente casos de teste complexos

## Troubleshooting

### Erro de banco de dados
```bash
php artisan migrate:fresh --env=testing
```

### Erro de autoload
```bash
composer dump-autoload
```

### Erro de cache
```bash
php artisan config:clear
php artisan cache:clear
``` 