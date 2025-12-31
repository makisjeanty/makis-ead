# Refatoração do Sistema de Webhooks - Documentação Completa

## 📋 Resumo Executivo

Esta implementação refatora completamente o sistema de processamento de webhooks da plataforma EAD, adicionando:

1. **Service Pattern** para separação de responsabilidades
2. **Resiliência** com retry automático e exponential backoff
3. **Idempotência** para prevenir processamento duplicado
4. **Auditoria** completa de todos os eventos de webhook
5. **Simulador de carga** em Python/FastAPI
6. **Testes automatizados** completos

---

## 🏗️ Arquitetura Implementada

### Componentes Criados

#### 1. WebhookService (`app/Services/WebhookService.php`)
- **Responsabilidade**: Processamento centralizado de webhooks
- **Funcionalidades**:
  - Idempotência via cache + database
  - Retry com exponential backoff (3 tentativas)
  - Logging estruturado
  - Auditoria de eventos
  - Validação de assinaturas

#### 2. WebhookController Refatorado (`app/Http/Controllers/WebhookController.php`)
- **Antes**: 122 linhas com lógica misturada
- **Depois**: 84 linhas focado apenas em HTTP
- **Melhoria**: Separação clara de responsabilidades

#### 3. WebhookEvent Model + Migration
- **Tabela**: `webhook_events`
- **Campos**:
  ```sql
  - id (PK)
  - event_id (unique) - Para idempotência
  - gateway (mercadopago|stripe)
  - payload (JSON)
  - status (success|failed)
  - result (JSON nullable)
  - processed_at (timestamp)
  - created_at, updated_at
  ```

#### 4. Correção de Bug Crítico
- **Arquivo**: `app/Models/Payment.php`
- **Bug**: Método `isPending()` não existia
- **Impacto**: Webhooks crashavam com "Call to undefined method"
- **Fix**: Método implementado e testado

---

## 🔄 Fluxo de Processamento

### MercadoPago Webhook

```
1. Request chega em /webhook/mercadopago
2. Controller valida estrutura básica
3. WebhookService::processMercadoPago()
4. Gera event_id único
5. Verifica duplicação (cache + DB)
6. Se não duplicado:
   a. Consulta status no gateway
   b. Busca payment local
   c. Confirma pagamento em transaction
   d. Cria enrollment
   e. Registra webhook_event
7. Retorna resultado estruturado
```

### Idempotência

```php
Event ID = "mercadopago_{payment_id}_{action}_{date_created}"

Verificação:
1. Cache::has("webhook_event:{event_id}") → Duplicado
2. WebhookEvent::where('event_id', $eventId)->exists() → Duplicado
3. Senão → Processa e marca ambos
```

### Retry Mechanism

```php
Tentativas: 3
Backoff: 
  - Tentativa 1: 0ms
  - Tentativa 2: 1000ms (1s)
  - Tentativa 3: 2000ms (2s)
Max delay: 10000ms (10s)
```

---

## 🧪 Testes Implementados

### PaymentFlowTest.php (11 testes)
- ✅ Criação de payment pendente
- ✅ Criptografia de metadata
- ✅ Transições de status
- ✅ Confirmação cria enrollment
- ✅ Não duplica enrollment
- ✅ Gateways retornam instâncias corretas
- ✅ Exception para gateway inválido
- ✅ Scopes de Payment funcionam

### WebhookProcessingTest.php (4 testes)
- ✅ Armazenamento de eventos
- ✅ Scopes de WebhookEvent
- ✅ Método isPending() funciona
- ✅ Unicidade de event_id

### Como Executar

```bash
# Migrar banco
php artisan migrate:fresh

# Todos os testes
php artisan test

# Apenas testes de pagamento
php artisan test --filter=PaymentFlowTest

# Apenas testes de webhook
php artisan test --filter=WebhookProcessingTest
```

---

## 🐍 Simulador de Webhooks (FastAPI)

### Instalação

```bash
pip install -r webhook_simulator_requirements.txt
```

### Execução

```bash
python webhook_simulator.py
# Servidor roda em http://localhost:8000
```

### Endpoints Disponíveis

#### 1. Webhook Único
```bash
POST /simulate/single

Body:
{
  "target_url": "http://makis-ead.local/webhook/mercadopago",
  "gateway": "mercadopago",
  "delay_ms": 0
}

Response:
{
  "success": true,
  "gateway": "mercadopago",
  "status_code": 200,
  "response_time_ms": 145.23,
  "payload": {...},
  "response": {"status": "success", "payment_id": 123}
}
```

#### 2. Teste de Carga
```bash
POST /simulate/load-test

Body:
{
  "target_url": "http://makis-ead.local/webhook/mercadopago",
  "gateway": "mercadopago",
  "total_requests": 100,
  "concurrent_requests": 10,
  "delay_ms": 0
}

Response:
{
  "test_id": "uuid-xxx",
  "status": "started",
  "message": "Load test started with 100 requests",
  "check_status_url": "/test-results/uuid-xxx"
}
```

#### 3. Verificar Resultados
```bash
GET /test-results/{test_id}

Response:
{
  "status": "completed",
  "started_at": "2025-12-30T03:00:00Z",
  "completed_at": "2025-12-30T03:01:15Z",
  "stats": {
    "total_sent": 100,
    "successful": 98,
    "failed": 2,
    "duplicate_responses": 0,
    "avg_response_time_ms": 125.45,
    "min_response_time_ms": 89.12,
    "max_response_time_ms": 234.67,
    "errors": [...]
  }
}
```

#### 4. Teste de Idempotência
```bash
POST /simulate/duplicate-test

Params:
- target_url: http://makis-ead.local/webhook/mercadopago
- gateway: mercadopago
- duplicate_count: 5

Response:
{
  "test": "duplicate_idempotency",
  "gateway": "mercadopago",
  "payload": {...},
  "results": [
    {"attempt": 1, "status_code": 200, "response": {"status": "success"}},
    {"attempt": 2, "status_code": 200, "response": {"status": "duplicate"}},
    {"attempt": 3, "status_code": 200, "response": {"status": "duplicate"}},
    ...
  ],
  "summary": {
    "total_attempts": 5,
    "duplicate_detected": 4
  }
}
```

### Exemplo de Uso Completo

```bash
# 1. Iniciar simulador
python webhook_simulator.py

# 2. Em outro terminal, testar webhook único
curl -X POST http://localhost:8000/simulate/single \
  -H "Content-Type: application/json" \
  -d '{
    "target_url": "http://localhost:8000/webhook/mercadopago",
    "gateway": "mercadopago"
  }'

# 3. Teste de carga
curl -X POST http://localhost:8000/simulate/load-test \
  -H "Content-Type: application/json" \
  -d '{
    "target_url": "http://localhost:8000/webhook/mercadopago",
    "gateway": "mercadopago",
    "total_requests": 1000,
    "concurrent_requests": 50
  }'

# 4. Verificar resultados (substituir {test_id})
curl http://localhost:8000/test-results/{test_id}
```

---

## 📊 Métricas e Monitoramento

### Logs Estruturados

Todos os eventos geram logs com contexto completo:

```php
Log::info('MercadoPago payment confirmed', [
    'payment_id' => $payment->id,
    'order_id' => $payment->order_id,
    'amount' => $payment->amount
]);

Log::warning("Webhook processing attempt {$attempt} failed", [
    'error' => $e->getMessage()
]);
```

### Auditoria via Banco

Consultas úteis:

```sql
-- Eventos por gateway (últimas 24h)
SELECT gateway, status, COUNT(*) as total
FROM webhook_events
WHERE processed_at >= NOW() - INTERVAL 24 HOUR
GROUP BY gateway, status;

-- Taxa de sucesso
SELECT 
  gateway,
  SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as success_rate
FROM webhook_events
GROUP BY gateway;

-- Eventos duplicados
SELECT event_id, COUNT(*) as attempts
FROM webhook_events
GROUP BY event_id
HAVING attempts > 1;
```

---

## 🔒 Segurança

### Validação de Assinaturas

```php
// Stripe (implementado)
$gateway->verifyWebhook($payload, $signature);

// MercadoPago (TODO - atualmente stub)
// Implementar validação via x-signature header
```

### Dados Sensíveis

```php
// Metadata criptografado automaticamente
'metadata' => 'encrypted:json' // em Payment model
```

---

## 🚀 Próximos Passos

1. **Implementar validação real de webhooks MercadoPago**
   - Verificar x-signature header
   - Validar contra secret key

2. **Adicionar queue processing**
   ```php
   ProcessWebhook::dispatch($webhookData)->onQueue('webhooks');
   ```

3. **Alertas de falhas**
   - Notificar se taxa de falha > 10%
   - Alertar se nenhum webhook em X minutos

4. **Dashboard de métricas**
   - Gráfico de volume por gateway
   - Taxa de sucesso em tempo real
   - Latência média

5. **Testes de integração E2E**
   - Criar sandbox de gateways
   - Simular fluxo completo

---

## 📝 Changelog

### v1.0.0 - 2025-12-30

**Added**
- WebhookService com retry e idempotência
- Tabela webhook_events para auditoria
- Simulador FastAPI com testes de carga
- 15 testes automatizados (PHPUnit)
- Factories para Course e Order

**Fixed**
- Bug crítico: Payment::isPending() não existia
- Payment::create() agora inclui user_id

**Changed**
- WebhookController refatorado para usar service
- Logs mais estruturados e informativos

---

## 👥 Autores

- Sistema refatorado por: AI Assistant (Verdent)
- Data: 2025-12-30

## 📄 Licença

Mesmo do projeto principal (Makis EAD)
