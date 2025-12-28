# PLANO DE TESTES DAS ROTAS - MAKIS EAD

## 📋 RESUMO EXECUTIVO

Este documento apresenta um plano completo de testes para todas as rotas identificadas na aplicação Laravel Makis EAD, baseado na análise do código-fonte.

---

## 🔍 ROTAS IDENTIFICADAS E CATEGORIZAÇÃO

### 1. ROTAS PÚBLICAS (Não requerem autenticação)

#### 1.1 Homepage e Navegação Geral
```bash
GET /                                    # Página inicial com cursos em destaque
GET /cursos                              # Listagem de todos os cursos
GET /cursos/{slug}                       # Página individual do curso
GET /sitemap.xml                         # Sitemap para SEO
GET /contact                             # Página de contato
POST /contact                            # Submissão do formulário de contato
```

#### 1.2 Carrinho de Compras
```bash
GET /carrinho                            # Visualizar carrinho
POST /carrinho/adicionar/{course}        # Adicionar curso ao carrinho
DELETE /carrinho/remover/{item}          # Remover item específico
DELETE /carrinho/limpar                  # Limpar carrinho completo
```

#### 1.3 Webhooks (Pagamentos)
```bash
POST /webhook/mercadopago               # Webhook MercadoPago
POST /webhook/stripe                    # Webhook Stripe
POST /webhook/stripe/subscription       # Webhook Assinaturas Stripe
POST /webhook/moncash/wallet            # Webhook MonCash
```

#### 1.4 Funcionalidades Gerais
```bash
POST /currency/set                      # Troca de moeda
GET /pricing                            # Página de planos e preços
```

### 2. ROTAS PROTEGIDAS (Requerem autenticação + verificação)

#### 2.1 Área do Aluno
```bash
GET /aluno/dashboard                    # Dashboard do aluno
GET /aluno/meus-cursos                  # Meus cursos matriculados
GET /aluno/curso/{slug}/aula/{lesson?}  # Sala de aula - assistir aulas
```

#### 2.2 Sistema de Pagamentos
```bash
GET /checkout                           # Página de checkout
POST /checkout/process                  # Processar pagamento
GET /checkout/success                   # Página de sucesso
GET /checkout/failure                   # Página de falha
GET /checkout/pending                   # Página de pagamento pendente
```

#### 2.3 Carteira Digital
```bash
GET /wallet/                            # Dashboard da carteira
GET /wallet/deposit                     # Página de depósito
POST /wallet/deposit                    # Processar depósito
GET /wallet/deposit/success             # Confirmação de depósito
GET /wallet/deposit/failure             # Falha no depósito
GET /wallet/history                     # Histórico de transações
```

#### 2.4 Sistema de Assinaturas
```bash
POST /subscription/checkout             # Checkout de assinatura
GET /subscription/success               # Confirmação de assinatura
GET /subscription/cancel                # Cancelar assinatura
GET /subscription/dashboard             # Dashboard de assinaturas
GET /subscription/portal                # Portal de gestão
POST /subscription/cancel-subscription  # Cancelar assinatura
POST /subscription/resume               # Retomar assinatura
```

#### 2.5 Redirecionamentos
```bash
GET /dashboard                          # Redireciona para /aluno/dashboard
GET /perfil                             # Redireciona para /aluno/meus-cursos
```

#### 2.6 Autenticação
```bash
# Rotas do Laravel Breeze (auth.php)
GET|POST /login                         # Login
GET|POST /register                      # Registro
GET|POST /forgot-password               # Esqueci minha senha
GET|POST /reset-password                # Reset de senha
GET|POST /email/verify                  # Verificação de email
```

---

## 🧪 PLANO DE TESTES DETALHADO

### FASE 1: TESTES DE CONECTIVIDADE

#### 1.1 Verificar Serviços
```bash
# Verificar se Laravel está respondendo
curl -I http://localhost:8000

# Verificar se Nginx está ativo
curl -I http://localhost:8000/health

# Verificar banco de dados
php artisan migrate:status
```

#### 1.2 Testes de Homepage
```bash
# Teste 1: Homepage
curl -X GET http://localhost:8000/
Expected: 200 OK + HTML da página inicial

# Teste 2: Listagem de cursos
curl -X GET http://localhost:8000/cursos
Expected: 200 OK + Lista de cursos

# Teste 3: Curso específico (precisa existir um curso)
curl -X GET http://localhost:8000/cursos/primeiro-curso
Expected: 200 OK + Página do curso
```

### FASE 2: TESTES DE FUNCIONALIDADES PÚBLICAS

#### 2.1 Testes do Carrinho
```bash
# Teste 4: Visualizar carrinho vazio
curl -X GET http://localhost:8000/carrinho
Expected: 200 OK + Página do carrinho

# Teste 5: Adicionar curso ao carrinho
curl -X POST http://localhost:8000/carrinho/adicionar/1 \
  -H "X-CSRF-TOKEN: {csrf_token}"
Expected: 302 Redirect + Carrinho atualizado
```

#### 2.2 Testes de Contato
```bash
# Teste 6: Página de contato
curl -X GET http://localhost:8000/contact
Expected: 200 OK + Formulário de contato

# Teste 7: Submissão do formulário
curl -X POST http://localhost:8000/contact \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "name=Test&email=test@example.com&message=Test message"
Expected: 302 Redirect + Mensagem de sucesso
```

### FASE 3: TESTES DE AUTENTICAÇÃO

#### 3.1 Fluxo de Registro
```bash
# Teste 8: Registro de usuário
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
Expected: 302 Redirect + Usuário criado
```

#### 3.2 Fluxo de Login
```bash
# Teste 9: Login
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=test@example.com&password=password123"
Expected: 302 Redirect + Token/Session criado
```

### FASE 4: TESTES DA ÁREA DO ALUNO

#### 4.1 Dashboard do Aluno
```bash
# Teste 10: Dashboard autenticado
curl -X GET http://localhost:8000/aluno/dashboard \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Dashboard do aluno

# Teste 11: Meus cursos
curl -X GET http://localhost:8000/aluno/meus-cursos \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Lista de cursos matriculados
```

#### 4.2 Sala de Aula
```bash
# Teste 12: Acessar aula
curl -X GET http://localhost:8000/aluno/curso/primeiro-curso/aula/1 \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Player de vídeo/aula

# Teste 13: Primeira aula (sem parâmetro)
curl -X GET http://localhost:8000/aluno/curso/primeiro-curso \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Primeira aula do curso
```

### FASE 5: TESTES DE PAGAMENTOS

#### 5.1 Checkout
```bash
# Teste 14: Página de checkout
curl -X GET http://localhost:8000/checkout \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Formulário de pagamento

# Teste 15: Processar pagamento
curl -X POST http://localhost:8000/checkout/process \
  -H "Cookie: {session_cookie}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "payment_method=stripe&course_id=1"
Expected: 302 Redirect + Redirecionamento para gateway
```

### FASE 6: TESTES DA CARTEIRA

#### 6.1 Gestão de Carteira
```bash
# Teste 16: Dashboard da carteira
curl -X GET http://localhost:8000/wallet/ \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Dashboard da carteira

# Teste 17: Depósito na carteira
curl -X GET http://localhost:8000/wallet/deposit \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Formulário de depósito

# Teste 18: Processar depósito
curl -X POST http://localhost:8000/wallet/deposit \
  -H "Cookie: {session_cookie}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "amount=100&currency=HTG"
Expected: 302 Redirect + Redirecionamento para pagamento
```

### FASE 7: TESTES DE ASSINATURAS

#### 7.1 Sistema de Assinaturas
```bash
# Teste 19: Página de preços
curl -X GET http://localhost:8000/pricing
Expected: 200 OK + Planos de assinatura

# Teste 20: Checkout de assinatura
curl -X POST http://localhost:8000/subscription/checkout \
  -H "Cookie: {session_cookie}" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "plan_id=basic"
Expected: 302 Redirect + Gateway de pagamento

# Teste 21: Dashboard de assinaturas
curl -X GET http://localhost:8000/subscription/dashboard \
  -H "Cookie: {session_cookie}"
Expected: 200 OK + Dashboard de assinaturas
```

### FASE 8: TESTES DE WEBSHOOKS

#### 8.1 Webhooks de Pagamento
```bash
# Teste 22: Webhook Stripe
curl -X POST http://localhost:8000/webhook/stripe \
  -H "Content-Type: application/json" \
  -d '{"type": "payment_intent.succeeded", "data": {...}}'
Expected: 200 OK + Acknowledgment

# Teste 23: Webhook MercadoPago
curl -X POST http://localhost:8000/webhook/mercadopago \
  -H "Content-Type: application/json" \
  -d '{"type": "payment", "data": {...}}'
Expected: 200 OK + Acknowledgment
```

### FASE 9: TESTES DE VALIDAÇÃO E SEGURANÇA

#### 9.1 Validação de CSRF
```bash
# Teste 24: Tentativa sem CSRF token
curl -X POST http://localhost:8000/carrinho/adicionar/1 \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "course_id=1"
Expected: 419 CSRF Token Mismatch
```

#### 9.2 Controle de Acesso
```bash
# Teste 25: Acesso não autorizado
curl -X GET http://localhost:8000/aluno/dashboard
Expected: 302 Redirect para login

# Teste 26: Rotas protegidas sem autenticação
curl -X GET http://localhost:8000/checkout
Expected: 302 Redirect para login
```

#### 9.3 Validação de Parâmetros
```bash
# Teste 27: Curso inexistente
curl -X GET http://localhost:8000/cursos/curso-inexistente
Expected: 404 Not Found

# Teste 28: Aula inexistente
curl -X GET http://localhost:8000/aluno/curso/curso-inexistente/aula/999 \
  -H "Cookie: {session_cookie}"
Expected: 404 Not Found
```

### FASE 10: TESTES DE PERFORMANCE

#### 10.1 Tempo de Resposta
```bash
# Teste 29: Benchmark da homepage
time curl -X GET http://localhost:8000/
Expected: < 500ms

# Teste 30: Benchmark da listagem de cursos
time curl -X GET http://localhost:8000/cursos
Expected: < 1000ms
```

---

## 📊 CASOS DE TESTE ESPECÍFICOS POR FUNCIONALIDADE

### 🛒 CARRINHO DE COMPRAS
1. **Adicionar curso ao carrinho**
   - Pré-condição: Curso publicado disponível
   - Ação: POST /carrinho/adicionar/{course}
   - Resultado esperado: Curso adicionado ao carrinho, redirecionamento

2. **Remover item do carrinho**
   - Pré-condição: Item no carrinho
   - Ação: DELETE /carrinho/remover/{item}
   - Resultado esperado: Item removido, carrinho atualizado

3. **Limpar carrinho**
   - Pré-condição: Itens no carrinho
   - Ação: DELETE /carrinho/limpar
   - Resultado esperado: Carrinho vazio

### 👤 ÁREA DO ALUNO
1. **Dashboard do aluno**
   - Pré-condição: Usuário autenticado
   - Ação: GET /aluno/dashboard
   - Resultado esperado: Estatísticas e cursos em andamento

2. **Assistir aula**
   - Pré-condição: Matrícula no curso
   - Ação: GET /aluno/curso/{slug}/aula/{lesson}
   - Resultado esperado: Player de vídeo/aula funcionando

3. **Progresso do curso**
   - Pré-condição: Aulas assistidas
   - Ação: GET /aluno/meus-cursos
   - Resultado esperado: Lista com progresso de cada curso

### 💳 SISTEMA DE PAGAMENTOS
1. **Processar checkout**
   - Pré-condição: Carrinho com itens, usuário autenticado
   - Ação: POST /checkout/process
   - Resultado esperado: Redirecionamento para gateway

2. **Webhook de confirmação**
   - Pré-condição: Pagamento processado
   - Ação: POST /webhook/{gateway}
   - Resultado esperado: Pedido confirmado, usuário matriculado

3. **Histórico de pagamentos**
   - Pré-condição: Usuário autenticado com compras
   - Ação: GET /wallet/history
   - Resultado esperado: Lista de transações

### 🎮 SISTEMA DE GAMIFICAÇÃO (API Python)
1. **Verificar estatísticas**
   - Pré-condição: Usuário autenticado
   - Ação: GET http://localhost:8001/dashboard/stats
   - Resultado esperado: XP, streak, engagement

2. **Validar resposta de exercício**
   - Pré-condição: Usuário fazendo exercício
   - Ação: POST http://localhost:8001/learn/check-answer
   - Resultado esperado: XP concedido, feedback

### 📱 RESPONSIVIDADE
1. **Mobile - Homepage**
   - Dispositivo: Mobile (320px)
   - Ação: GET / em viewport mobile
   - Resultado esperado: Layout responsivo funcionando

2. **Tablet - Carrinho**
   - Dispositivo: Tablet (768px)
   - Ação: GET /carrinho em viewport tablet
   - Resultado esperado: Layout adaptativo

---

## 🚨 CASOS DE TESTE DE FALHA

### 🔒 SEGURANÇA
1. **SQL Injection**
   ```bash
   curl -X GET "http://localhost:8000/cursos/'; DROP TABLE courses; --"
   Expected: 404 ou sanitização da entrada
   ```

2. **XSS Prevention**
   ```bash
   curl -X POST http://localhost:8000/contact \
     -d "name=<script>alert('xss')</script>"
   Expected: Input sanitizado
   ```

3. **CSRF Protection**
   ```bash
   curl -X POST http://localhost:8000/carrinho/adicionar/1 \
     -d "course_id=1"  # Sem CSRF token
   Expected: 419 Token Mismatch
   ```

### 🛡️ AUTENTICAÇÃO
1. **Acesso não autorizado**
   ```bash
   curl -X GET http://localhost:8000/aluno/dashboard
   Expected: 302 Redirect para login
   ```

2. **Token expirado**
   ```bash
   curl -X GET http://localhost:8000/aluno/dashboard \
     -H "Cookie: session=expired_token"
   Expected: 302 Redirect para login
   ```

### 📊 PERFORMANCE
1. **Alta carga**
   ```bash
   # Simular 100 usuários concurrentes
   ab -n 100 -c 10 http://localhost:8000/
   Expected: Tempos de resposta aceitáveis
   ```

2. **Banco de dados**
   ```bash
   # Verificar queries lentas
   php artisan optimize
   Expected: Performance otimizada
   ```

---

## 📈 MÉTRICAS DE SUCESSO

### ✅ Critérios de Aprovação
- **Taxa de sucesso**: > 95%
- **Tempo de resposta médio**: < 500ms
- **Tempo de resposta p95**: < 1000ms
- **Disponibilidade**: > 99%
- **Sem erros críticos**: 0

### 📊 Relatório de Testes
```
Total de casos de teste: 30
Executados: 0 (pendente ambiente)
Aprovados: 0
Reprovados: 0
Taxa de sucesso: N/A
```

---

## 🔧 EXECUÇÃO DOS TESTES

### Pré-requisitos
1. ✅ Ambiente Docker configurado
2. ✅ Banco de dados MySQL funcionando
3. ✅ Dependências Composer instaladas
4. ✅ Migrations executadas
5. ✅ Seeders executados
6. ✅ Usuário de teste criado

### Comandos para Executar Testes
```bash
# 1. Iniciar ambiente
./setup.sh

# 2. Executar testes manuais (exemplo)
curl -X GET http://localhost:8000/

# 3. Executar testes automatizados (se implementados)
php artisan test

# 4. Verificar logs
tail -f storage/logs/laravel.log
```

### Scripts de Automação
```bash
# Script para testar todas as rotas públicas
./test_public_routes.sh

# Script para testar rotas autenticadas
./test_authenticated_routes.sh

# Script para testar webhooks
./test_webhooks.sh
```

---

## 🎯 CONCLUSÃO

Este plano de testes abrange todas as rotas identificadas na aplicação Makis EAD, incluindo:

- ✅ **30 casos de teste** principais
- ✅ **Funcionalidades completas**: Carrinho, Pagamentos, Gamificação
- ✅ **Segurança**: CSRF, XSS, SQL Injection
- ✅ **Performance**: Tempos de resposta, carga
- ✅ **Responsividade**: Mobile e Desktop

### Próximos Passos
1. **Configurar ambiente** de testes
2. **Executar casos de teste** sequencialmente
3. **Documentar resultados** de cada teste
4. **Corrigir falhas** identificadas
5. **Implementar automação** dos testes

---

*Plano de Testes gerado em: $(date)*
*Total de rotas mapeadas: 25+*
*Casos de teste planejados: 30*
