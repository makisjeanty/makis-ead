# Rotação de credenciais MercadoPago

Este documento descreve os passos recomendados para rotacionar as credenciais do MercadoPago e atualizar os segredos usados pelo repositório e CI.

Nomes de segredos (GitHub Actions)
- `MERCADOPAGO_ACCESS_TOKEN` — token de acesso usado pela SDK/integração.
- `MERCADOPAGO_WEBHOOK_KEY` — chave usada para verificar assinaturas HMAC dos webhooks.
- `MERCADOPAGO_MODE` — `sandbox` ou `production`.

Passos recomendados
1. No painel MercadoPago (conta) gere um novo `access_token` e um novo `webhook_key` (ou `notification secret`) para o ambiente desejado (sandbox/production).
2. Atualize seu `.env` localmente com os novos valores:

```env
MERCADOPAGO_ACCESS_TOKEN=APP_USR_xxx
MERCADOPAGO_WEBHOOK_KEY=sk_test_xxx
MERCADOPAGO_MODE=sandbox
```

3. Atualize os segredos do GitHub (no repositório):

```bash
# usando GitHub CLI (gh)
gh secret set MERCADOPAGO_ACCESS_TOKEN --body "<new_access_token>"
gh secret set MERCADOPAGO_WEBHOOK_KEY --body "<new_webhook_key>"
gh secret set MERCADOPAGO_MODE --body "sandbox"
```

4. Atualize o endpoint de webhook no painel do MercadoPago para apontar ao ambiente (ex.: `https://staging.example.com/webhook/mercadopago`). Use o modo sandbox para testes.
5. Envie um evento de teste pelo painel (ou crie um pagamento sandbox) e verifique os logs do endpoint e a aplicação:
   - Verifique `storage/logs/laravel.log` ou os logs de webhook registrados.
   - Verifique que o handler do webhook rejeita payloads sem assinatura ou com assinatura inválida.
6. Quando confirmado, revogue o token/chave antiga no painel do MercadoPago.
7. Opcional: para maior segurança, agende rotações periódicas e registre a operação em `ROTATION_CHECKLIST.md`.

Notas técnicas
- A verificação HMAC implementada em `app/Services/Gateways/MercadoPagoGateway.php` procura pelos headers `x-meli-signature`, `x-mercadopago-signature` ou `x-mp-signature`.
- O valor esperado pode estar em base64 ou hex; ambos são verificados.
- Se o provedor não enviar assinatura, o gateway fará um fallback chamando a API do MercadoPago para validar o `paymentId` presente no payload.

Precauções
- Nunca comite tokens nem chaves no repositório.
- Atualize apenas os segredos no GitHub (não o `.env.example`).
- Teste em `sandbox` antes de atualizar produção.
