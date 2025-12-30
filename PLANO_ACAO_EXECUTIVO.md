# 🎯 PLANO DE AÇÃO EXECUTIVO - MAKIS EAD

## 📊 Status Atual da Análise

**Data:** 2025-12-30  
**Análise Completa:** ✅ Concluída  
**Problemas Identificados:** 47  
**Débito Técnico:** ~80-120 horas

### Distribuição de Severidade
- 🔴 **CRITICAL:** 12 problemas
- 🟠 **HIGH:** 15 problemas  
- 🟡 **MEDIUM:** 16 problemas
- 🟢 **LOW:** 4 problemas

---

## 🚨 PROBLEMAS CRÍTICOS QUE BLOQUEIAM PRODUÇÃO

### 1. ⚠️ ACESSO A CURSOS PAGOS SEM PAGAMENTO (CRITICAL)
**Arquivo:** `app/Policies/CoursePolicy.php`  
**Problema:** Qualquer usuário pode assistir cursos pagos gratuitamente  
**Impacto:** 💰 Perda de receita, fraude

```php
// ATUAL (VULNERÁVEL):
public function view(User $user, Course $course): bool
{
    return $course->is_published || $user->role === 'admin';
    // ❌ Não verifica se usuário COMPROU o curso!
}

// DEVE SER:
public function view(User $user, Course $course): bool
{
    if ($user->role === 'admin') return true;
    if (!$course->is_published) return false;
    
    // ✅ Verifica se curso é grátis OU se user comprou
    return $course->isFree() || $user->hasEnrollment($course->id);
}
```

**Ação Imediata:** CORRIGIR HOJE

---

### 2. ⚠️ ENROLLMENT CRIADO SEM PAGAMENTO (CRITICAL)
**Arquivo:** `app/Services/StudentProgressService.php:44-57`  
**Problema:** Assistir uma aula cria enrollment automaticamente

```php
// VULNERÁVEL:
public function updateLessonProgress(...)
{
    $enrollment = Enrollment::firstOrCreate([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]); // ❌ Qualquer um pode criar enrollment só assistindo!
}
```

**Impacto:** 💰 Acesso grátis a todos os cursos  
**Ação Imediata:** CORRIGIR HOJE

---

### 3. ⚠️ WEBHOOK MERCADOPAGO SEM VALIDAÇÃO (CRITICAL)
**Arquivo:** `app/Services/Gateways/MercadoPagoGateway.php:74-79`

```php
public function verifyWebhook(array $data): bool
{
    return true;  // ❌ ACEITA QUALQUER WEBHOOK FALSO!
}
```

**Impacto:** 🔓 Qualquer pessoa pode criar pagamentos falsos  
**Ação Imediata:** IMPLEMENTAR VALIDAÇÃO

---

### 4. ⚠️ CHECKOUT SEM TRANSAÇÃO DATABASE (CRITICAL)
**Arquivo:** `app/Http/Controllers/CheckoutController.php:51-86`

```php
public function process(Request $request)
{
    // ❌ Sem DB::transaction()!
    $payment = $paymentService->createPayment(...);
    $result = $paymentService->processPayment(...);
    session()->forget('cart');
    
    // ⚠️ Se falhar aqui, payment fica órfão!
}
```

**Impacto:** 🐛 Dados inconsistentes, pagamentos órfãos  
**Ação Imediata:** ADICIONAR TRANSAÇÃO

---

### 5. ⚠️ MASS ASSIGNMENT VULNERABILITIES (CRITICAL)
**Arquivos:**
- `app/Models/CartItem.php` - SEM $fillable/$guarded
- `app/Models/Enrollment.php:9` - $guarded = []
- `app/Models/Module.php:12` - $guarded = []

**Impacto:** 🔓 Usuário pode modificar qualquer campo  
**Ação Imediata:** DEFINIR $fillable

---

### 6. ⚠️ MÉTODOS INEXISTENTES (CRITICAL - ERRO FATAL)

#### 6.1 User->courses() não existe
```php
// app/Services/CourseEnrollmentService.php:23
if ($user->courses()->where(...)->exists()) { // ❌ ERRO FATAL!
```

#### 6.2 Course->isPublished() não existe
```php
// app/Services/CourseEnrollmentService.php:19
if (!$course->isPublished()) { // ❌ ERRO FATAL!
```

#### 6.3 Wallet->withdraw() não existe
```php
// app/Http/Controllers/Student/CheckoutController.php:29
$user->wallet->withdraw(...); // ❌ ERRO FATAL!
// Método correto é debit()
```

**Ação Imediata:** CORRIGIR TODOS

---

### 7. ⚠️ CONTROLLERS VAZIOS (CRITICAL)
- `app/Http/Controllers/SubscriptionController.php` - VAZIO
- `app/Http/Controllers/StripeWebhookController.php` - VAZIO

**Impacto:** 🔥 Rotas retornam 500 error  
**Ação Imediata:** IMPLEMENTAR OU REMOVER ROTAS

---

## 🔥 PLANO DE CORREÇÃO IMEDIATA (HOJE/AMANHÃ)

### Fase 1: Segurança Crítica (2-3 horas)
```
[✓] 1. Corrigir CoursePolicy para validar enrollment
[✓] 2. Definir $fillable em CartItem
[✓] 3. Trocar $guarded = [] por $fillable específico
[✓] 4. Adicionar verificação de ownership no CartController
```

### Fase 2: Correção de Bugs Fatais (2-3 horas)
```
[✓] 5. Adicionar User->courses() relationship
[✓] 6. Adicionar Course->isPublished() method
[✓] 7. Corrigir Wallet->withdraw() para debit()
[✓] 8. Adicionar Course->lessons() relationship
```

### Fase 3: Transações e Consistência (3-4 horas)
```
[✓] 9. Adicionar DB::transaction() no checkout
[✓] 10. Corrigir enrollment automático no StudentProgress
[✓] 11. Implementar validação webhook MercadoPago
[✓] 12. Corrigir limpeza de carrinho
```

### Fase 4: Testes (2-3 horas)
```
[✓] 13. Criar testes para CoursePolicy
[✓] 14. Criar testes para checkout flow
[✓] 15. Criar testes para enrollment
[✓] 16. Executar suite completa
```

**TOTAL ESTIMADO: 10-13 horas**

---

## 📋 PRÓXIMA SPRINT (SEMANA 1)

### Performance Critical (P1)
- [ ] Resolver N+1 em StudentProgressService
- [ ] Resolver N+1 no Dashboard
- [ ] Adicionar índices no banco

### Funcionalidades Incompletas (P1)
- [ ] Implementar SubscriptionController
- [ ] Implementar StripeWebhookController  
- [ ] Implementar envio de emails

---

## 🎯 OBJETIVO PARA HOJE

**Meta:** Corrigir os 7 problemas CRÍTICOS de segurança

1. ✅ Análise completa concluída
2. ⏳ Implementar correções de segurança
3. ⏳ Testes das correções
4. ⏳ Commit e documentação

---

## 📁 ARQUIVOS DE REFERÊNCIA

- `MAPEAMENTO_FLUXOS_CRITICOS.md` - Fluxos detalhados
- `WEBHOOK_REFACTORING_DOCUMENTATION.md` - Webhooks refatorados
- Este arquivo - Plano de ação

---

## 🚀 COMEÇAR AGORA?

Execute na ordem:
```bash
# 1. Ver próxima correção crítica
php artisan make:test CoursePolicyTest

# 2. Implementar correções
# 3. Rodar testes
php artisan test

# 4. Commit
git add .
git commit -m "fix: correct critical security vulnerabilities"
```
