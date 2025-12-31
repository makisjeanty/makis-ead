# Rotation checklist — credenciais sensíveis

Siga estas etapas para rotacionar credenciais expostas e restaurar segurança do projeto.

1) Banco de dados
- Gere nova senha para o usuário do banco (ex.: `makis_ead_user`) no servidor MySQL.
- Atualize a variável `DB_PASSWORD` no seu `.env` local.
- Reinicie o serviço da aplicação se necessário.

Comandos (exemplo MySQL):
```sql
ALTER USER 'makis_ead_user'@'%' IDENTIFIED BY 'NEW_PASSWORD';
FLUSH PRIVILEGES;
```

2) APP_KEY
- Já foi gerada uma nova `APP_KEY` localmente; certifique-se de atualizar qualquer ambiente de produção se necessário e re-gerar chaves compatíveis.

3) MercadoPago
- No dashboard MercadoPago: regenere o `access_token` e `public_key` (sandbox/produção conforme o caso).
- Atualize `MERCADOPAGO_ACCESS_TOKEN` e `MERCADOPAGO_PUBLIC_KEY` no `.env` local e nos segredos do CI/CD.

4) Stripe
- No Stripe Dashboard: crie novas chaves de API (Publishable/Secret) e recrie o webhook secret se necessário.
- Atualize `STRIPE_PUBLIC_KEY`, `STRIPE_SECRET_KEY` e `STRIPE_WEBHOOK_SECRET` nos ambientes locais e CI.

5) Webhooks & CI
- Atualize os segredos/configurações dos webhooks (Stripe, MercadoPago) para usar as novas chaves.
- Atualize segredos no provedor de CI (GitHub Actions secrets, etc.).

6) Comunicar equipe
- Informe os colaboradores para re-clonar o repositório (histórico reescrito):
```bash
git clone https://github.com/makisjeanty/makis-ead.git
```

7) Verificação final
- Execute varredura por padrões residuais:
```bash
git log --all -S "YOUR_OLD_SECRET"
```

8) Melhores práticas
- Nunca comitar `.env` com valores reais.
- Use secret managers (AWS Secrets Manager, GitHub Secrets, Vault) para produção.
- Habilite secret scanning no GitHub para detectar commits futuros.
