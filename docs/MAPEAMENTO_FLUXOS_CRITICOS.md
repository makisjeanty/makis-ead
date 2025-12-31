# MAPEAMENTO DE FLUXOS CRÍTICOS DA PLATAFORMA EAD

## SUMÁRIO EXECUTIVO

Este documento mapeia todos os fluxos de negócio críticos da plataforma EAD, identificando:
- Arquivos envolvidos em cada etapa
- Validações aplicadas
- Pontos sem tratamento de erro
- Possíveis race conditions
- Transações database necessárias

---

## 1. FLUXO DE COMPRA/CHECKOUT

### 1.1 Visão Geral

**Fluxo Principal:**
```
Seleção do Curso → Carrinho → Checkout → Gateway de Pagamento → Webhook → Enrollment
```

### 1.2 Arquivos Envolvidos (em ordem de execução)

#### ETAPA 1: Adicionar ao Carrinho
**Arquivo:** `app/Http/Controllers/CartController.php` (método `add()`)

**Fluxo:**
```php
POST /carrinho/adicionar/{course}
↓
CartController::add(Course $course)
↓
CartItem::create([
    'user_id' => auth()->id(),
    'session_id' => session()->getId(),
    'course_id' => $course->id,
    'price' => $course->price,
])
```

**Validações Aplicadas:**
- ✅ Model binding valida existência do curso
- ❌ NÃO VALIDA: Se usuário já possui o curso
- ❌ NÃO VALIDA: Se curso já está no carrinho (permite duplicatas)
- ❌ NÃO VALIDA: Se curso está publicado
- ❌ NÃO VALIDA: Preço do curso (pode ter mudado)

**Pontos de Falha:**
1. **Sem validação de curso já comprado** - usuário pode adicionar curso que já possui
2. **Sem validação de duplicatas** - pode adicionar mesmo curso múltiplas vezes
3. **Race Condition:** Preço do curso pode mudar entre adicionar ao carrinho e finalizar compra
4. **Sem transação database** - operação única, mas sem validações críticas

#### ETAPA 2: Visualizar Checkout
**Arquivo:** `app/Http/Controllers/CheckoutController.php` (método `index()`)

**Fluxo:**
```php
GET /checkout
↓
CheckoutController::index()
↓
$cartItems = CartItem::with('course')->where('user_id', auth()->id())->get()
↓
$courses = Course::whereIn('id', $courseIds)->get()
```

**Validações Aplicadas:**
- ✅ Verifica se carrinho está vazio
- ✅ Carrega cursos relacionados (eager loading)
- ❌ NÃO VALIDA: Se cursos ainda estão publicados
- ❌ NÃO VALIDA: Se preços dos cursos mudaram
- ❌ NÃO VALIDA: Se usuário já possui algum dos cursos

**Pontos de Falha:**
1. **Preço inconsistente** - calcula total baseado em `cartItems.price`, não no preço atual do curso
2. **Cursos despublicados** - pode exibir cursos que não estão mais disponíveis

#### ETAPA 3: Processar Checkout
**Arquivo:** `app/Http/Controllers/CheckoutController.php` (método `process()`)

**Fluxo:**
```php
POST /checkout/process
↓
CheckoutController::process(Request $request)
↓
1. Valida gateway
2. Carrega itens do carrinho
3. Cria Order
4. Cria Payment (via PaymentService)
5. Processa pagamento no gateway
6. Limpa carrinho da sessão (mas NÃO do database!)
7. Redireciona para gateway
```

**Código Crítico:**
```php
// Cria Order
$order = Order::create([
    'user_id' => auth()->id(),
    'order_number' => Order::generateOrderNumber(),
    'total' => $total,
    'status' => Order::STATUS_PENDING,
    'payment_method' => $request->gateway,
    'metadata' => [
        'course_id' => $courses->first()->id, // ⚠️ PROBLEMA: Só salva primeiro curso
        'courses' => $courses->pluck('id')->toArray(),
    ],
]);

// Cria Payment
$payment = $this->paymentService->createPayment($order, $request->gateway);

// Limpa carrinho
session()->forget('cart'); // ⚠️ PROBLEMA: Limpa SESSION, não CartItem do DB
```

**Validações Aplicadas:**
- ✅ Valida gateway (mercadopago|stripe)
- ✅ Verifica se carrinho não está vazio
- ❌ NÃO VALIDA: Se cursos estão publicados
- ❌ NÃO VALIDA: Se usuário já possui os cursos
- ❌ NÃO VALIDA: Se preço mudou desde adicionar ao carrinho

**Problemas Críticos:**
1. **SEM TRANSAÇÃO DATABASE** - Se criar Order suceder mas Payment falhar, fica inconsistente
2. **Metadata incompleto** - Salva apenas primeiro curso em `course_id`, mas `courses` tem todos
3. **Limpeza de carrinho incorreta** - Limpa `session()->forget('cart')` mas CartItem persiste no DB
4. **Race Condition:** Entre validar carrinho e criar order, preços podem mudar
5. **Sem validação de ownership** - Pode comprar curso que já possui

**Transação Necessária:**
```php
DB::transaction(function() use ($request, $cartItems, $courses, $total) {
    // Validar que usuário não possui nenhum dos cursos
    // Criar Order
    // Criar Payment
    // Criar preferência no gateway
    // Apenas no final, se tudo OK, limpar CartItem do DB
});
```

#### ETAPA 4: PaymentService
**Arquivo:** `app/Services/PaymentService.php`

**Métodos Envolvidos:**
1. `createPayment()` - Cria registro de pagamento
2. `processPayment()` - Processa via gateway
3. `confirmPayment()` - Confirma pagamento via webhook
4. `createEnrollmentFromOrder()` - Cria matrícula

**Fluxo de createPayment:**
```php
Payment::create([
    'order_id' => $order->id,
    'user_id' => $order->user_id,
    'gateway' => $gateway,
    'amount' => $order->total,
    'status' => Payment::STATUS_PENDING,
    'metadata' => $metadata,
])
```

**Validações:**
- ❌ NÃO VALIDA: Se order existe
- ❌ NÃO VALIDA: Se amount é positivo
- ❌ NÃO TEM TRANSAÇÃO

**Fluxo de processPayment:**
```php
$gateway = $this->getGateway($payment->gateway);
$result = $gateway->createPayment($payment, $paymentData);

if ($result['success']) {
    $payment->update([
        'transaction_id' => $result['transaction_id'] ?? null,
        'metadata' => array_merge($payment->metadata ?? [], $result['metadata'] ?? []),
    ]);
}
```

**Problemas:**
1. **SEM TRANSAÇÃO** - Update de payment fora de transação
2. **Try/catch incompleto** - Marca como failed mas não reverte order

**Fluxo de confirmPayment (webhook):**
```php
$payment->markAsCompleted();
$payment->order->markAsPaid();
$this->createEnrollmentFromOrder($payment->order);
```

**Problemas Críticos:**
1. **SEM TRANSAÇÃO DATABASE** ⚠️⚠️⚠️
2. **Enrollment pode falhar silenciosamente** - Usa `firstOrCreate` que pode dar erro
3. **Apenas cria enrollment para UM curso** - Ignora orders com múltiplos cursos

#### ETAPA 5: Gateway (MercadoPago)
**Arquivo:** `app/Services/Gateways/MercadoPagoGateway.php`

**Fluxo:**
```php
$preference = new Preference();
$item->unit_price = (float) $payment->amount;
$preference->external_reference = $payment->id; // ✅ Bom
$preference->notification_url = route('webhook.mercadopago');
$preference->save();
```

**Validações:**
- ❌ NÃO VALIDA: Se access_token está configurado
- ❌ NÃO VALIDA: Se amount é positivo
- ✅ Retorna array com success/error

**Problemas:**
1. **Sem validação de ambiente** - Pode misturar sandbox e produção
2. **verifyWebhook() retorna sempre true** - Sem verificação real

#### ETAPA 6: Webhook
**Arquivo:** `app/Http/Controllers/WebhookController.php` → `app/Services/WebhookService.php`

**Fluxo Completo:**
```php
POST /webhook/mercadopago
↓
WebhookController::mercadopago(Request $request)
↓
WebhookService::processMercadoPago(array $webhookData)
↓
1. Valida tipo de evento
2. Verifica duplicata (idempotência)
3. Processa com retry (3 tentativas)
4. handleMercadoPagoPayment()
   ↓
   4.1 Busca status do pagamento no MP
   4.2 Valida se status = 'approved'
   4.3 Busca Payment no DB
   4.4 Verifica se não foi processado
   4.5 Confirma pagamento (PaymentService::confirmPayment)
       ↓
       4.5.1 markAsCompleted()
       4.5.2 order->markAsPaid()
       4.5.3 createEnrollmentFromOrder()
5. Registra evento no WebhookEvent
```

**Validações Aplicadas:**
- ✅ Validação de idempotência (cache + database)
- ✅ Retry com backoff exponencial
- ✅ Validação de status approved
- ✅ Verifica se payment já foi processado
- ❌ NÃO VALIDA assinatura do webhook (sempre retorna true)

**Pontos Críticos:**
1. **Busca de Payment problemática:**
```php
// Tenta external_reference primeiro
if ($externalReference) {
    $payment = Payment::find($externalReference);
}
// Fallback para transaction_id
return Payment::where('transaction_id', $gatewayPaymentId)->first();
```
**Problema:** `external_reference` pode não estar no webhook, depende de como MP envia

2. **Transaction scope correto:**
```php
return DB::transaction(function() use ($payment, $paymentInfo) {
    $this->paymentService->confirmPayment($payment, $paymentInfo);
    // ✅ BOM: Usa transação
});
```

3. **Enrollment pode falhar:**
```php
protected function createEnrollmentFromOrder(Order $order): void
{
    $courseId = $order->metadata['course_id'] ?? null;
    
    if ($courseId) {
        \App\Models\Enrollment::firstOrCreate([
            'user_id' => $order->user_id,
            'course_id' => $courseId,
        ], [
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);
    }
}
```
**Problemas:**
- ❌ Se `course_id` não existir no metadata, não cria enrollment
- ❌ Se order tem múltiplos cursos, apenas o primeiro é matriculado
- ❌ Não incrementa `students_count` do curso
- ❌ Usa `firstOrCreate` que pode dar erro de duplicata sem tratamento

### 1.3 Fluxo Alternativo: Compra via Wallet
**Arquivo:** `app/Http/Controllers/Student/CheckoutController.php` (método `purchase()`)

**Fluxo:**
```php
POST /student/purchase/{slug}
↓
1. Carrega curso
2. Verifica se já possui (✅ BOM)
3. Tenta debitar wallet
4. Se sucesso:
   - Cria Payment
   - Cria Enrollment
5. Se falha (saldo insuficiente):
   - Redireciona para recarga
```

**Validações:**
- ✅ Verifica se já possui curso
- ✅ Usa wallet->withdraw() que valida saldo
- ❌ NÃO USA TRANSAÇÃO DATABASE

**Problema Crítico:**
```php
try {
    $user->wallet->withdraw($course->price, ...);
    
    Payment::create([...]); // ⚠️ Pode falhar
    
    Enrollment::create([...]); // ⚠️ Pode falhar
    
} catch (Exception $e) {
    // ⚠️ Se withdraw sucedeu mas Payment/Enrollment falhou,
    // o dinheiro foi debitado mas o curso não foi entregue!
}
```

**Correção Necessária:**
```php
DB::transaction(function() use ($user, $course) {
    $user->wallet->withdraw($course->price, ...);
    Payment::create([...]);
    Enrollment::create([...]);
    $course->increment('students_count');
});
```

### 1.4 Pontos de Falha - Resumo

| # | Ponto de Falha | Severidade | Localização |
|---|----------------|------------|-------------|
| 1 | Pode comprar curso já adquirido | Alta | CartController::add, CheckoutController::process |
| 2 | Duplicatas no carrinho | Média | CartController::add |
| 3 | Race condition no preço | Alta | Entre CartItem e Order |
| 4 | Sem transação em checkout | Crítica | CheckoutController::process |
| 5 | Limpeza incorreta de carrinho | Média | CheckoutController::process (limpa session, não DB) |
| 6 | Enrollment apenas para 1 curso | Crítica | PaymentService::createEnrollmentFromOrder |
| 7 | Compra via wallet sem transação | Crítica | Student\CheckoutController::purchase |
| 8 | Webhook sem validação de assinatura | Alta | MercadoPagoGateway::verifyWebhook |
| 9 | Students_count não incrementado | Média | Enrollment criado sem atualizar contador |

### 1.5 Race Conditions Identificadas

#### RC1: Preço do Curso
```
T1: User adiciona curso X ao carrinho (preço = 100)
T2: Admin altera preço do curso X para 200
T3: User finaliza checkout
Resultado: User paga 100, mas curso agora vale 200
```
**Impacto:** Perda de receita
**Solução:** Validar preço no checkout, atualizar CartItem se mudou

#### RC2: Curso Despublicado
```
T1: User adiciona curso X ao carrinho
T2: Admin despublica curso X
T3: User finaliza checkout
Resultado: User compra curso indisponível
```
**Impacto:** Venda de produto indisponível
**Solução:** Validar is_published no checkout

#### RC3: Webhook Duplicado
```
T1: Webhook1 recebido, inicia processamento
T2: Webhook2 (duplicata) recebido, inicia processamento
T3: Ambos tentam marcar payment como completed
Resultado: Possível duplicação de enrollment
```
**Impacto:** Duplicação de dados
**Solução:** ✅ JÁ RESOLVIDO via idempotência + cache

### 1.6 Transações Database Necessárias

#### Transação 1: Checkout Process
```php
DB::transaction(function() {
    // 1. Validar cursos publicados e não já adquiridos
    // 2. Criar Order
    // 3. Criar Payment
    // 4. Criar preferência no gateway
    // 5. Limpar CartItem do database
});
```

#### Transação 2: Webhook Payment Confirmation
```php
DB::transaction(function() {
    // 1. Marcar payment como completed
    // 2. Marcar order como paid
    // 3. Criar enrollment(s) para TODOS os cursos
    // 4. Incrementar students_count de cada curso
    // 5. Limpar CartItem se ainda existir
});
```

#### Transação 3: Compra via Wallet
```php
DB::transaction(function() {
    // 1. Debitar wallet
    // 2. Criar Payment
    // 3. Criar Enrollment
    // 4. Incrementar students_count
});
```

---

## 2. FLUXO DE AUTENTICAÇÃO/AUTORIZAÇÃO

### 2.1 Visão Geral

**Fluxo de Login:**
```
Formulário de Login → LoginRequest → AuthenticatedSessionController → Dashboard
```

**Fluxo de Registro:**
```
Formulário de Registro → RegisteredUserController → Event(Registered) → Dashboard
```

**Fluxo de Autorização de Curso:**
```
Tentativa de Acesso → CoursePolicy → ClassroomController
```

### 2.2 Arquivos Envolvidos

#### ETAPA 1: Registro
**Arquivo:** `app/Http/Controllers/Auth/RegisteredUserController.php` (método `store()`)

**Fluxo:**
```php
POST /register
↓
RegisteredUserController::store(Request $request)
↓
1. Valida dados
2. Cria User
3. Dispara evento Registered
4. Faz login automático
5. Redireciona para dashboard
```

**Validações Aplicadas:**
- ✅ Nome obrigatório, max 255
- ✅ Email obrigatório, único, válido
- ✅ Senha obrigatória, confirmada
- ❌ NÃO CRIA WALLET automaticamente
- ❌ NÃO DEFINE role (fica null)
- ❌ NÃO DEFINE status (fica null)

**Código:**
```php
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    // ❌ Faltando: 'role' => 'student'
    // ❌ Faltando: 'status' => 'active'
]);
```

**Problemas:**
1. **Campos obrigatórios não preenchidos** - role e status ficam null
2. **Wallet não criada** - Pode dar erro ao tentar acessar wallet
3. **Sem transação** - Se evento Registered falhar, user fica criado

#### ETAPA 2: Login
**Arquivo:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (método `store()`)

**Fluxo:**
```php
POST /login
↓
LoginRequest::authenticate()
↓
Auth::attempt(['email' => $email, 'password' => $password])
↓
Session regenerate
```

**Validações:**
- ✅ Email e senha validados via LoginRequest
- ✅ Throttling de tentativas
- ❌ NÃO VALIDA: Se user está ativo (status = 'active')
- ❌ NÃO VALIDA: Se email foi verificado

**Problema:**
```php
// LoginRequest não verifica status do usuário
Auth::attempt($credentials); // Permite login mesmo se status = 'suspended'
```

#### ETAPA 3: Verificação de Email
**Arquivo:** `app/Http/Controllers/Auth/VerifyEmailController.php`

**Middleware usado:** `auth`, `signed`, `throttle:6,1`

**Validações:**
- ✅ URL assinada (signed route)
- ✅ Throttling
- ❌ NÃO CRIA WALLET após verificação

### 2.3 Autorização de Acesso a Cursos

#### CoursePolicy
**Arquivo:** `app/Policies/CoursePolicy.php`

**Métodos:**

1. **view(User $user, Course $course)**
```php
return $course->is_published || $user->role === 'admin';
```
**Validações:**
- ✅ Admin pode ver qualquer curso
- ✅ User só vê curso publicado
- ❌ NÃO VALIDA: Se user tem enrollment (pode ver curso grátis sem matrícula?)

2. **enroll(User $user, Course $course)**
```php
return $course->is_published && $user->hasVerifiedEmail();
```
**Validações:**
- ✅ Curso publicado
- ✅ Email verificado
- ❌ NÃO VALIDA: Se user já está matriculado
- ❌ NÃO VALIDA: Se user tem saldo (para cursos pagos)

#### ClassroomController
**Arquivo:** `app/Http/Controllers/Student/ClassroomController.php` (método `watch()`)

**Fluxo de Autorização:**
```php
GET /aluno/curso/{slug}/aula/{lesson}
↓
ClassroomController::watch($courseSlug, $lessonId)
↓
$this->authorize('view', $course); // ⚠️ Usa policy incorreta
```

**Problema Crítico:**
```php
$this->authorize('view', $course);
// ✅ Valida se curso está publicado OU user é admin
// ❌ MAS NÃO VALIDA se user POSSUI o curso (enrollment)
```

**Resultado:** Qualquer usuário pode assistir qualquer curso publicado, mesmo sem pagar!

**Correção Necessária:**
```php
// Adicionar método à CoursePolicy
public function watch(User $user, Course $course): bool
{
    if ($user->role === 'admin') return true;
    if (!$course->is_published) return false;
    if ($course->isFree()) return true; // ✅ Curso grátis, todos podem assistir
    
    // ✅ Para cursos pagos, precisa ter enrollment
    return $user->enrollments()->where('course_id', $course->id)->exists();
}

// Usar no controller
$this->authorize('watch', $course);
```

### 2.4 Fluxo de Roles e Permissões

**Roles Existentes:**
- `admin` - Acesso ao Filament Admin Panel
- `student` - Usuário padrão (mas não é setado no registro!)
- `null` - Usuários criados via RegisteredUserController ficam sem role ⚠️

**Verificações de Acesso:**

1. **Filament Admin Panel:**
```php
// User.php
public function canAccessPanel(Panel $panel): bool
{
    return $this->role === 'admin' && $this->status === 'active';
}
```
✅ Correto

2. **Área do Aluno:**
```php
// routes/web.php
Route::middleware(['auth', 'verified'])->prefix('aluno')->group(...)
```
❌ NÃO VALIDA role, qualquer usuário autenticado pode acessar

3. **CoursePolicy:**
Usa `$user->role === 'admin'` em vários lugares
✅ Correto

### 2.5 Pontos de Falha - Resumo

| # | Ponto de Falha | Severidade | Localização |
|---|----------------|------------|-------------|
| 1 | Registro sem definir role/status | Alta | RegisteredUserController::store |
| 2 | Wallet não criada no registro | Alta | RegisteredUserController::store |
| 3 | Login sem validar status | Média | AuthenticatedSessionController::store |
| 4 | Autorização 'view' permite acesso sem enrollment | Crítica | CoursePolicy::view + ClassroomController |
| 5 | Cursos pagos acessíveis sem pagar | Crítica | ClassroomController::watch |

### 2.6 Correções Necessárias

#### Correção 1: Registro Completo
```php
// RegisteredUserController::store
DB::transaction(function() use ($request) {
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'student', // ✅ Adicionar
        'status' => 'active', // ✅ Adicionar
    ]);
    
    // ✅ Criar wallet
    $user->wallet()->create([
        'balance' => 0,
        'currency' => 'HTG',
        'status' => 'active',
    ]);
    
    event(new Registered($user));
    Auth::login($user);
});
```

#### Correção 2: Login com Validação de Status
```php
// LoginRequest::authenticate
$credentials = $this->only('email', 'password');

if (Auth::attempt($credentials)) {
    $user = Auth::user();
    
    // ✅ Validar status
    if ($user->status !== 'active') {
        Auth::logout();
        throw ValidationException::withMessages([
            'email' => 'Sua conta está inativa. Entre em contato com o suporte.',
        ]);
    }
    
    // Continuar...
}
```

#### Correção 3: Autorização Correta de Curso
```php
// CoursePolicy - Adicionar método watch
public function watch(User $user, Course $course): bool
{
    // Admin pode tudo
    if ($user->role === 'admin') return true;
    
    // Curso precisa estar publicado
    if (!$course->is_published) return false;
    
    // Curso grátis, todos podem assistir
    if ($course->isFree()) return true;
    
    // Curso pago, precisa ter enrollment
    return $user->enrollments()->where('course_id', $course->id)->exists();
}

// ClassroomController::watch
$this->authorize('watch', $course); // ✅ Usar método correto
```

---

## 3. FLUXO DE PROGRESSO DO ALUNO

### 3.1 Visão Geral

**Fluxo:**
```
Assistir Aula → StudentProgressService → LessonCompletion → Enrollment.progress_percentage → Certificado
```

### 3.2 Arquivos Envolvidos

#### ETAPA 1: Assistir Aula
**Arquivo:** `app/Http/Controllers/Student/ClassroomController.php` (método `watch()`)

**Fluxo:**
```php
GET /aluno/curso/{slug}/aula/{lesson}
↓
ClassroomController::watch($courseSlug, $lessonId)
↓
1. Carrega curso com cache
2. Autoriza acesso (PolicyBug - não valida enrollment!)
3. Carrega aula
4. Obtém progresso do curso
5. Atualiza progresso da aula
6. Retorna view
```

**Código:**
```php
// Get course progress for the current user
$courseProgress = $this->progressService->getCourseProgress(auth()->user(), $course);

// Update progress for the current lesson
if ($currentLesson) {
    $this->progressService->updateLessonProgress(auth()->user(), $currentLesson, $course);
}
```

**Validações:**
- ✅ Valida existência do curso (model binding)
- ✅ Carrega aulas ordenadas
- ❌ NÃO VALIDA: Se user tem acesso ao curso (bug na policy)
- ❌ NÃO VALIDA: Se aula pertence ao curso
- ✅ Usa cache (performance)

**Problemas:**
1. **Atualiza progresso sempre que carrega a página** - Deveria ser endpoint separado
2. **Não valida ownership da lição** - Pode passar lesson_id de outro curso
3. **Cache pode ficar stale** - Se progresso for atualizado, cache não é invalidado

#### ETAPA 2: StudentProgressService
**Arquivo:** `app/Services/StudentProgressService.php`

**Método Principal: updateLessonProgress()**
```php
public function updateLessonProgress(User $user, Lesson $lesson, Course $course): void
{
    DB::transaction(function () use ($user, $lesson, $course) {
        // 1. Get or create enrollment
        $enrollment = Enrollment::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        $totalLessons = $course->lessons()->count();
        if ($totalLessons == 0) return;

        // 2. Mark lesson as completed
        $this->markLessonAsCompleted($user, $lesson);

        // 3. Calculate new progress
        $completedLessons = $this->getCompletedLessonsCount($user, $course);
        $progressPercentage = min(100, (int) (($completedLessons / $totalLessons) * 100));

        // 4. Update enrollment
        $enrollment->update(['progress_percentage' => $progressPercentage]);

        // 5. Check if course is completed
        if ($progressPercentage >= 100 && is_null($enrollment->completed_at)) {
            $enrollment->update(['completed_at' => now()]);
        }
    });
}
```

**Validações:**
- ✅ Usa transação database
- ✅ firstOrCreate para enrollment (idempotente)
- ✅ Calcula progresso corretamente
- ✅ Marca curso como completo
- ❌ NÃO VALIDA: Se lesson pertence ao course
- ❌ NÃO VALIDA: Se user tem acesso ao curso

**Problema Crítico:**
```php
// Cria enrollment se não existir
$enrollment = Enrollment::firstOrCreate([...]);
```
**Impacto:** Qualquer usuário pode criar enrollment só de assistir aula, sem pagar!

**Método: markLessonAsCompleted()**
```php
protected function markLessonAsCompleted(User $user, Lesson $lesson): void
{
    LessonCompletion::firstOrCreate([
        'user_id' => $user->id,
        'lesson_id' => $lesson->id,
    ], [
        'completed_at' => now(),
    ]);
}
```
**Validações:**
- ✅ Idempotente (firstOrCreate)
- ❌ NÃO VALIDA ownership

**Método: getCompletedLessonsCount()**
```php
public function getCompletedLessonsCount(User $user, Course $course): int
{
    return LessonCompletion::forUser($user->id)
        ->forCourse($course->id)
        ->count();
}
```

**Usa Scope no Model:**
```php
// LessonCompletion.php
public function scopeForCourse($query, $courseId)
{
    return $query->whereHas('lesson.module', function ($q) use ($courseId) {
        $q->where('course_id', $courseId);
    });
}
```
✅ Correto

**Método: getCourseProgress()**
```php
public function getCourseProgress(User $user, Course $course): array
{
    $totalLessons = $course->lessons()->count();
    $completedLessons = $this->getCompletedLessonsCount($user, $course);
    $progressPercentage = $totalLessons > 0 ? min(100, (int) (($completedLessons / $totalLessons) * 100)) : 0;

    $modulesWithProgress = $course->modules->map(function ($module) use ($user) {
        $totalLessons = $module->lessons->count();
        $completedLessons = 0;
        
        foreach ($module->lessons as $lesson) {
            $lessonKey = "course_{$module->course_id}_lesson_{$lesson->id}";
            $completedLessons += in_array("course_{$module->course_id}_lesson_{$lesson->id}", $user->metadata['completed_lessons'] ?? []) ? 1 : 0;
        }
        
        $moduleProgress = $totalLessons > 0 ? min(100, (int) (($completedLessons / $totalLessons) * 100)) : 0;
        
        return [
            'module' => $module,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => $moduleProgress,
        ];
    });

    return [
        'total_lessons' => $totalLessons,
        'completed_lessons' => $completedLessons,
        'progress_percentage' => $progressPercentage,
        'modules' => $modulesWithProgress,
    ];
}
```

**Problema Grave:**
```php
// ⚠️ BUG: Usa user.metadata['completed_lessons'] que NÃO EXISTE
$completedLessons += in_array("course_{$module->course_id}_lesson_{$lesson->id}", 
    $user->metadata['completed_lessons'] ?? []) ? 1 : 0;
```

**Deve usar LessonCompletion:**
```php
foreach ($module->lessons as $lesson) {
    if (LessonCompletion::where('user_id', $user->id)
        ->where('lesson_id', $lesson->id)->exists()) {
        $completedLessons++;
    }
}
```

### 3.3 Relação com Certificados

**Status Atual:** NÃO IMPLEMENTADO

**Lógica Esperada:**
```php
if ($enrollment->completed_at !== null) {
    // Gerar certificado
    // Tabela: certificates
    // Campos: user_id, course_id, certificate_number, issued_at, pdf_url
}
```

**Onde Implementar:**
- `StudentProgressService::updateLessonProgress()` - Após marcar curso como completo
- Criar `CertificateService`
- Criar model `Certificate`
- Criar migration

### 3.4 Pontos de Falha - Resumo

| # | Ponto de Falha | Severidade | Localização |
|---|----------------|------------|-------------|
| 1 | Cria enrollment sem validar pagamento | Crítica | StudentProgressService::updateLessonProgress |
| 2 | Atualiza progresso ao carregar página | Média | ClassroomController::watch |
| 3 | Não valida se lesson pertence ao course | Alta | StudentProgressService::updateLessonProgress |
| 4 | Bug no cálculo de módulo (usa metadata inexistente) | Alta | StudentProgressService::getCourseProgress |
| 5 | Cache não é invalidado ao atualizar progresso | Média | ClassroomController::watch |
| 6 | Certificados não implementados | Baixa | N/A |

### 3.5 Race Conditions

#### RC1: Múltiplas Atualizações de Progresso
```
T1: User assiste aula 1, updateLessonProgress inicia
T2: User (em outra aba) assiste aula 2, updateLessonProgress inicia
T3: T1 calcula progresso = 10% (1 aula)
T4: T2 calcula progresso = 10% (1 aula, não vê completion de T1 ainda)
T5: T1 atualiza enrollment.progress_percentage = 10%
T6: T2 atualiza enrollment.progress_percentage = 10%
Resultado: Progresso deveria ser 20%, mas ficou 10%
```

**Solução Atual:** ✅ DB::transaction com lockForUpdate no Wallet, mas não no Enrollment

**Correção Necessária:**
```php
DB::transaction(function () use ($user, $lesson, $course) {
    // Lock enrollment for update
    $enrollment = Enrollment::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->lockForUpdate()
        ->first();
    
    // Continuar lógica...
});
```

### 3.6 Transações Database

#### Transação 1: Update Lesson Progress (já existe)
```php
DB::transaction(function () {
    // 1. Lock enrollment
    // 2. Mark lesson as completed
    // 3. Recalculate progress
    // 4. Update enrollment
    // 5. If 100%, mark as completed and generate certificate
});
```

#### Transação 2: Complete Course (novo)
```php
DB::transaction(function () use ($enrollment) {
    // 1. Marcar enrollment como completed
    // 2. Gerar certificado
    // 3. Registrar log
    // 4. Enviar email de parabenização
});
```

---

## 4. FLUXO DE WALLET/CRÉDITOS

### 4.1 Visão Geral

**Fluxo de Recarga:**
```
Formulário de Depósito → WalletController → MonCashGateway → Webhook → Wallet.credit()
```

**Fluxo de Uso:**
```
Compra de Curso → Wallet.debit() → Payment → Enrollment
```

### 4.2 Arquivos Envolvidos

#### ETAPA 1: Página de Depósito
**Arquivo:** `app/Http/Controllers/WalletController.php` (método `deposit()`)

```php
GET /wallet/deposit
↓
WalletController::deposit()
↓
$wallet = $user->getOrCreateWallet();
```

**Validações:**
- ✅ Middleware auth
- ✅ Cria wallet se não existir
- ❌ NÃO VALIDA: Se wallet está ativa

#### ETAPA 2: Processar Depósito
**Arquivo:** `app/Http/Controllers/WalletController.php` (método `processDeposit()`)

**Fluxo:**
```php
POST /wallet/deposit
↓
WalletController::processDeposit(Request $request)
↓
1. Valida amount (min: 10, max: 100000)
2. Valida gateway (apenas 'moncash')
3. Cria WalletTransaction com status 'pending'
4. Cria pagamento no MonCash
5. Redireciona para checkout MonCash
```

**Código:**
```php
$transaction = $wallet->transactions()->create([
    'type' => 'credit',
    'amount' => $amount,
    'balance_before' => $wallet->balance,
    'balance_after' => $wallet->balance, // ⚠️ Não muda ainda
    'reference_type' => 'deposit',
    'status' => 'pending',
    'description' => "Deposit via {$request->gateway}",
    'metadata' => [
        'gateway' => $request->gateway,
        'user_id' => $user->id,
    ],
]);

$result = $this->monCashGateway->createPayment($amount, "WALLET-{$transaction->id}");

if ($result['success']) {
    $transaction->update([
        'metadata' => array_merge($transaction->metadata ?? [], [
            'payment_token' => $result['payment_token'],
            'transaction_id' => $result['transaction_id'],
        ]),
    ]);
    
    return redirect($result['checkout_url']);
}
```

**Validações:**
- ✅ Validação de amount (min/max)
- ✅ Validação de gateway
- ✅ Cria transaction com status pending
- ❌ NÃO USA TRANSAÇÃO DATABASE
- ❌ Se createPayment falhar, transaction fica pending forever

**Problemas:**
1. **Sem transação database** - Se update de metadata falhar, perde payment_token
2. **Transaction pending órfã** - Se createPayment falhar, precisa marcar como failed

**Correção Necessária:**
```php
DB::transaction(function() use ($wallet, $amount, $request) {
    $transaction = $wallet->transactions()->create([...]);
    
    try {
        $result = $this->monCashGateway->createPayment($amount, "WALLET-{$transaction->id}");
        
        if ($result['success']) {
            $transaction->update(['metadata' => ...]);
            return redirect($result['checkout_url']);
        } else {
            $transaction->markAsFailed();
            throw new \Exception($result['error']);
        }
    } catch (\Exception $e) {
        $transaction->markAsFailed();
        throw $e;
    }
});
```

#### ETAPA 3: Webhook de Confirmação
**Arquivo:** `app/Http/Controllers/WalletWebhookController.php` (método `moncash()`)

**Status:** Arquivo existe mas implementação está em `WebhookController` para MercadoPago/Stripe

**Lógica Esperada:**
```php
POST /webhook/moncash/wallet
↓
1. Validar assinatura do webhook
2. Buscar WalletTransaction pelo transaction_id
3. Validar status do pagamento no MonCash
4. Se aprovado:
   - Wallet::credit() (já tem transação interna)
   - Marcar WalletTransaction como completed
   - Atualizar balance_after
5. Se falhou:
   - Marcar WalletTransaction como failed
```

**Implementação Necessária:**
```php
public function moncash(Request $request)
{
    $transactionId = $request->input('transaction_id');
    
    // Buscar transaction
    $walletTransaction = WalletTransaction::where('metadata->transaction_id', $transactionId)
        ->where('status', 'pending')
        ->first();
    
    if (!$walletTransaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }
    
    // Validar no MonCash
    $gateway = new MonCashGateway();
    $paymentStatus = $gateway->getPaymentStatus($transactionId);
    
    if ($paymentStatus['status'] !== 'success') {
        $walletTransaction->markAsFailed();
        return response()->json(['status' => 'failed'], 200);
    }
    
    DB::transaction(function() use ($walletTransaction) {
        $wallet = $walletTransaction->wallet;
        
        // Creditar wallet (já tem transação interna)
        $wallet->credit(
            $walletTransaction->amount,
            'deposit',
            $walletTransaction->id,
            'Deposit confirmed'
        );
        
        // Marcar como completed
        $walletTransaction->update([
            'status' => 'completed',
            'balance_after' => $wallet->balance,
        ]);
    });
    
    return response()->json(['status' => 'success'], 200);
}
```

#### ETAPA 4: Model Wallet
**Arquivo:** `app/Models/Wallet.php`

**Método: credit()**
```php
public function credit(float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null, array $metadata = []): WalletTransaction
{
    if ($amount <= 0) {
        throw new \InvalidArgumentException('Credit amount must be greater than zero');
    }

    return DB::transaction(function () use ($amount, $referenceType, $referenceId, $description, $metadata) {
        // Lock wallet for update
        $lockedWallet = $this->lockForUpdate()->find($this->id);

        $balanceBefore = $lockedWallet->balance;
        $balanceAfter = $balanceBefore + $amount;

        // Create transaction
        $transaction = $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description ?? "Credit: {$referenceType}",
            'status' => 'completed',
            'metadata' => $metadata,
        ]);

        // Update wallet balance
        $lockedWallet->increment('balance', $amount);

        Log::info("Wallet credited successfully", [...]);

        return $transaction;
    });
}
```

**Validações:**
- ✅ Valida amount > 0
- ✅ Usa DB::transaction
- ✅ Usa lockForUpdate (previne race conditions)
- ✅ Cria transaction antes de atualizar balance
- ✅ Log completo

**Perfeito!** ✅

**Método: debit()**
```php
public function debit(float $amount, string $referenceType, ?int $referenceId = null, ?string $description = null, array $metadata = []): WalletTransaction
{
    if ($amount <= 0) {
        throw new \InvalidArgumentException('Debit amount must be greater than zero');
    }

    return DB::transaction(function () use ($amount, $referenceType, $referenceId, $description, $metadata) {
        // Lock wallet for update
        $lockedWallet = $this->lockForUpdate()->find($this->id);

        // Check balance
        if (!$lockedWallet->hasBalance($amount)) {
            $exception = new InsufficientBalanceException("Insufficient wallet balance. Required: {$amount}, Available: {$lockedWallet->balance}");
            
            Log::warning("Insufficient balance for debit", [...]);
            
            throw $exception;
        }

        $balanceBefore = $lockedWallet->balance;
        $balanceAfter = $balanceBefore - $amount;

        // Create transaction
        $transaction = $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description ?? "Debit: {$referenceType}",
            'status' => 'completed',
            'metadata' => $metadata,
        ]);

        // Update wallet balance
        $lockedWallet->decrement('balance', $amount);

        Log::info("Wallet debited successfully", [...]);

        return $transaction;
    });
}
```

**Validações:**
- ✅ Valida amount > 0
- ✅ Valida saldo suficiente
- ✅ Usa DB::transaction
- ✅ Usa lockForUpdate
- ✅ Lança InsufficientBalanceException
- ✅ Log completo

**Perfeito!** ✅

**Método: transferTo()**
```php
public function transferTo(Wallet $targetWallet, float $amount, ?string $description = null): array
{
    if ($amount <= 0) {
        throw new \InvalidArgumentException('Transfer amount must be greater than zero');
    }

    if ($this->currency !== $targetWallet->currency) {
        throw new \InvalidArgumentException('Cannot transfer between wallets with different currencies');
    }

    if (!$this->hasBalance($amount)) {
        throw new InsufficientBalanceException('Insufficient balance for transfer');
    }

    return DB::transaction(function () use ($targetWallet, $amount, $description) {
        // Debit from source wallet
        $debitTransaction = $this->debit(
            $amount,
            'transfer_out',
            $targetWallet->id,
            $description ?? "Transfer to wallet {$targetWallet->id}"
        );

        // Credit to target wallet
        $creditTransaction = $targetWallet->credit(
            $amount,
            'transfer_in',
            $this->id,
            $description ?? "Transfer from wallet {$this->id}"
        );

        Log::info("Fund transfer completed", [...]);

        return [
            'debit_transaction' => $debitTransaction,
            'credit_transaction' => $creditTransaction
        ];
    });
}
```

**Validações:**
- ✅ Valida amount > 0
- ✅ Valida mesma moeda
- ✅ Valida saldo
- ✅ Usa DB::transaction
- ✅ debit() e credit() já usam lockForUpdate internamente

**Observação:** `debit()` e `credit()` já usam `DB::transaction`, então há transações aninhadas. Laravel suporta isso com savepoints.

**Perfeito!** ✅

#### ETAPA 5: Uso de Wallet na Compra
**Arquivo:** `app/Http/Controllers/Student/CheckoutController.php` (método `purchase()`)

**Já analisado na seção 1.3**

**Problema:** Não usa transação database envolvendo wallet + payment + enrollment

### 4.3 Histórico de Transações
**Arquivo:** `app/Http/Controllers/WalletController.php` (método `history()`)

**Fluxo:**
```php
GET /wallet/history
↓
WalletController::history(Request $request)
↓
Filtros: type, status, from_date, to_date
↓
Paginação: 20 por página
```

**Validações:**
- ✅ Filtros validados com in_array
- ✅ Paginação
- ✅ Ordenação por data (latest)

**Perfeito!** ✅

### 4.4 Pontos de Falha - Resumo

| # | Ponto de Falha | Severidade | Localização |
|---|----------------|------------|-------------|
| 1 | Depósito sem transação database | Média | WalletController::processDeposit |
| 2 | Transaction pending órfã se gateway falhar | Média | WalletController::processDeposit |
| 3 | Webhook MonCash não implementado | Alta | WalletWebhookController::moncash |
| 4 | Compra via wallet sem transação | Crítica | Student\CheckoutController::purchase |

### 4.5 Race Conditions

#### RC1: Duplo Débito
```
T1: User compra curso A (debit 100)
T2: User compra curso B (debit 100)
Saldo inicial: 150

Sem lockForUpdate:
T1: Lê saldo = 150, OK
T2: Lê saldo = 150, OK
T1: Debita 100, saldo = 50
T2: Debita 100, saldo = 50 (deveria ser -50, ERRO!)
```

**Solução Atual:** ✅ `lockForUpdate()` no método `debit()`

**Resultado:**
```
T1: Lock + lê saldo = 150, OK
T2: Aguarda lock
T1: Debita 100, saldo = 50, release lock
T2: Lock + lê saldo = 50, FALHA (InsufficientBalanceException)
```

**Perfeito!** ✅

#### RC2: Duplo Crédito (Webhook)
```
T1: Webhook1 recebido, inicia credit()
T2: Webhook2 (duplicata) recebido, inicia credit()
Resultado: Saldo creditado 2x
```

**Solução:** Implementar idempotência no webhook (verificar se transaction já foi processada)

**Implementação Necessária:**
```php
public function moncash(Request $request)
{
    $transactionId = $request->input('transaction_id');
    
    // ✅ Buscar transaction com lockForUpdate
    $walletTransaction = WalletTransaction::where('metadata->transaction_id', $transactionId)
        ->lockForUpdate()
        ->first();
    
    if (!$walletTransaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }
    
    // ✅ Verificar se já foi processada
    if ($walletTransaction->status !== 'pending') {
        return response()->json(['status' => 'already_processed'], 200);
    }
    
    // Continuar...
}
```

### 4.6 Transações Database Necessárias

#### Transação 1: Process Deposit (correção)
```php
DB::transaction(function() {
    // 1. Criar WalletTransaction pending
    // 2. Criar pagamento no gateway
    // 3. Se sucesso: atualizar metadata
    // 4. Se falha: marcar como failed
});
```

#### Transação 2: Webhook Confirmation (novo)
```php
DB::transaction(function() {
    // 1. Lock WalletTransaction
    // 2. Verificar se não foi processada
    // 3. Validar status no gateway
    // 4. Creditar wallet (já tem transação interna)
    // 5. Marcar transaction como completed
});
```

#### Transação 3: Purchase with Wallet (correção)
```php
DB::transaction(function() {
    // 1. Debitar wallet (já tem transação interna)
    // 2. Criar Payment
    // 3. Criar Enrollment
    // 4. Incrementar students_count
});
```

---

## 5. RESUMO GERAL - PRIORIZAÇÃO DE CORREÇÕES

### 5.1 Crítico (Implementar Imediatamente)

1. **Autorização de Acesso a Cursos** (Fluxo 2)
   - **Problema:** Qualquer usuário pode assistir cursos pagos sem pagar
   - **Arquivo:** `app/Policies/CoursePolicy.php` + `app/Http/Controllers/Student/ClassroomController.php`
   - **Solução:** Adicionar método `watch()` na policy e validar enrollment

2. **Enrollment Sem Pagamento** (Fluxo 3)
   - **Problema:** `updateLessonProgress` cria enrollment sem validar pagamento
   - **Arquivo:** `app/Services/StudentProgressService.php`
   - **Solução:** Não criar enrollment, apenas atualizar se já existir

3. **Checkout Sem Transação** (Fluxo 1)
   - **Problema:** Criar Order e Payment sem transação database
   - **Arquivo:** `app/Http/Controllers/CheckoutController.php`
   - **Solução:** Envolver tudo em `DB::transaction()`

4. **Compra via Wallet Sem Transação** (Fluxo 1 + 4)
   - **Problema:** Débito pode suceder mas enrollment falhar
   - **Arquivo:** `app/Http/Controllers/Student/CheckoutController.php`
   - **Solução:** Envolver tudo em `DB::transaction()`

5. **Webhook MonCash Não Implementado** (Fluxo 4)
   - **Problema:** Depósitos via MonCash não são confirmados
   - **Arquivo:** `app/Http/Controllers/WalletWebhookController.php`
   - **Solução:** Implementar lógica completa de webhook

### 5.2 Alto (Implementar Esta Semana)

6. **Registro Incompleto** (Fluxo 2)
   - **Problema:** Usuários criados sem role/status/wallet
   - **Arquivo:** `app/Http/Controllers/Auth/RegisteredUserController.php`
   - **Solução:** Adicionar role, status e criar wallet em transação

7. **Enrollment Apenas Para 1 Curso** (Fluxo 1)
   - **Problema:** Orders com múltiplos cursos só matriculam no primeiro
   - **Arquivo:** `app/Services/PaymentService.php`
   - **Solução:** Iterar sobre `metadata['courses']` e criar enrollment para cada um

8. **Validação de Duplicatas no Carrinho** (Fluxo 1)
   - **Problema:** Pode adicionar mesmo curso múltiplas vezes
   - **Arquivo:** `app/Http/Controllers/CartController.php`
   - **Solução:** Verificar antes de criar CartItem

9. **Validação de Curso Já Comprado** (Fluxo 1)
   - **Problema:** Pode comprar curso que já possui
   - **Arquivo:** `app/Http/Controllers/CartController.php` + `CheckoutController.php`
   - **Solução:** Validar enrollment antes de adicionar ao carrinho

10. **Race Condition de Preço** (Fluxo 1)
    - **Problema:** Preço pode mudar entre adicionar ao carrinho e checkout
    - **Arquivo:** `app/Http/Controllers/CheckoutController.php`
    - **Solução:** Validar e atualizar preço no checkout

### 5.3 Médio (Implementar Este Mês)

11. **Bug no Cálculo de Progresso de Módulo** (Fluxo 3)
    - **Problema:** Usa `user.metadata['completed_lessons']` que não existe
    - **Arquivo:** `app/Services/StudentProgressService.php`
    - **Solução:** Usar `LessonCompletion` para calcular

12. **Limpeza de Carrinho Incorreta** (Fluxo 1)
    - **Problema:** Limpa session mas não CartItem do DB
    - **Arquivo:** `app/Http/Controllers/CheckoutController.php`
    - **Solução:** `CartItem::where('user_id', auth()->id())->delete()`

13. **Login Sem Validar Status** (Fluxo 2)
    - **Problema:** Usuários suspensos podem fazer login
    - **Arquivo:** `app/Http/Requests/Auth/LoginRequest.php`
    - **Solução:** Adicionar validação de status

14. **Depósito Sem Transação** (Fluxo 4)
    - **Problema:** Metadata pode não ser salva se falhar
    - **Arquivo:** `app/Http/Controllers/WalletController.php`
    - **Solução:** Envolver em `DB::transaction()`

15. **Webhook MercadoPago Sem Validação de Assinatura** (Fluxo 1)
    - **Problema:** `verifyWebhook()` sempre retorna true
    - **Arquivo:** `app/Services/Gateways/MercadoPagoGateway.php`
    - **Solução:** Implementar validação real

### 5.4 Baixo (Melhorias Futuras)

16. **Cache Não Invalidado** (Fluxo 3)
    - **Problema:** Progresso atualizado mas cache não limpo
    - **Arquivo:** `app/Http/Controllers/Student/ClassroomController.php`
    - **Solução:** `Cache::forget()` após atualizar progresso

17. **Atualização de Progresso na Página** (Fluxo 3)
    - **Problema:** Atualiza toda vez que carrega a página
    - **Arquivo:** `app/Http/Controllers/Student/ClassroomController.php`
    - **Solução:** Criar endpoint separado `POST /aluno/curso/{slug}/aula/{lesson}/complete`

18. **Students Count Não Atualizado** (Fluxo 1)
    - **Problema:** Enrollment criado mas contador não incrementado
    - **Arquivo:** `app/Services/PaymentService.php`
    - **Solução:** `$course->increment('students_count')` após enrollment

19. **Certificados Não Implementados** (Fluxo 3)
    - **Problema:** Curso completo mas sem certificado
    - **Arquivo:** Novo - `app/Services/CertificateService.php`
    - **Solução:** Criar service completo de certificados

20. **Validação de Lesson Ownership** (Fluxo 3)
    - **Problema:** Pode passar lesson_id de outro curso
    - **Arquivo:** `app/Services/StudentProgressService.php`
    - **Solução:** Validar `$lesson->module->course_id === $course->id`

---

## 6. CHECKLIST DE IMPLEMENTAÇÃO

### Semana 1 - Segurança Crítica
- [ ] Implementar `CoursePolicy::watch()` e usar no `ClassroomController`
- [ ] Remover `firstOrCreate` de enrollment em `StudentProgressService`
- [ ] Adicionar transação em `CheckoutController::process()`
- [ ] Adicionar transação em `Student\CheckoutController::purchase()`
- [ ] Implementar webhook MonCash completo

### Semana 2 - Correções de Enrollment
- [ ] Corrigir registro de usuário (role, status, wallet)
- [ ] Criar enrollment para TODOS os cursos na order
- [ ] Adicionar validação de duplicatas no carrinho
- [ ] Validar se curso já foi comprado
- [ ] Validar e atualizar preço no checkout

### Semana 3 - Bugfixes e Melhorias
- [ ] Corrigir cálculo de progresso de módulo
- [ ] Corrigir limpeza de carrinho
- [ ] Adicionar validação de status no login
- [ ] Adicionar transação em depósito wallet
- [ ] Implementar validação de webhook MercadoPago

### Semana 4 - Otimizações
- [ ] Invalidar cache ao atualizar progresso
- [ ] Criar endpoint separado para marcar aula completa
- [ ] Incrementar students_count ao criar enrollment
- [ ] Implementar sistema de certificados
- [ ] Validar ownership de lesson

---

## 7. SCRIPTS SQL ÚTEIS PARA AUDITORIA

### Verificar Inconsistências de Enrollment
```sql
-- Enrollments sem Payment correspondente (possível fraude)
SELECT e.*, u.email, c.title 
FROM enrollments e
JOIN users u ON e.user_id = u.id
JOIN courses c ON e.course_id = c.id
LEFT JOIN payments p ON p.user_id = e.user_id AND p.course_id = e.course_id
WHERE p.id IS NULL
AND c.price > 0;

-- Payments sem Enrollment (bug de criação)
SELECT p.*, u.email, c.title
FROM payments p
JOIN users u ON p.user_id = u.id
LEFT JOIN courses c ON p.course_id = c.id
LEFT JOIN enrollments e ON e.user_id = p.user_id AND e.course_id = p.course_id
WHERE p.status = 'completed'
AND e.id IS NULL;
```

### Verificar Inconsistências de Wallet
```sql
-- Wallet Transactions pending há mais de 24h
SELECT wt.*, u.email
FROM wallet_transactions wt
JOIN wallets w ON wt.wallet_id = w.id
JOIN users u ON w.user_id = u.id
WHERE wt.status = 'pending'
AND wt.created_at < NOW() - INTERVAL 24 HOUR;

-- Verificar se balance da wallet bate com transactions
SELECT 
    w.id,
    w.balance as wallet_balance,
    COALESCE(SUM(CASE WHEN wt.type = 'credit' THEN wt.amount ELSE -wt.amount END), 0) as calculated_balance
FROM wallets w
LEFT JOIN wallet_transactions wt ON w.id = wt.wallet_id AND wt.status = 'completed'
GROUP BY w.id, w.balance
HAVING wallet_balance != calculated_balance;
```

### Verificar Inconsistências de Progresso
```sql
-- Enrollments com progresso > 100%
SELECT * FROM enrollments WHERE progress_percentage > 100;

-- Enrollments marcados como completos mas progresso < 100%
SELECT * FROM enrollments 
WHERE completed_at IS NOT NULL 
AND progress_percentage < 100;

-- Lesson Completions para cursos não matriculados
SELECT lc.*, u.email, c.title
FROM lesson_completions lc
JOIN lessons l ON lc.lesson_id = l.id
JOIN modules m ON l.module_id = m.id
JOIN courses c ON m.course_id = c.id
JOIN users u ON lc.user_id = u.id
LEFT JOIN enrollments e ON e.user_id = lc.user_id AND e.course_id = c.id
WHERE e.id IS NULL;
```

---

**FIM DO MAPEAMENTO**

Este documento deve ser revisado a cada 2 semanas conforme implementações são feitas.
Última atualização: 2025-12-30
