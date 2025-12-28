# MercadoPago Rotation Guide

Steps to safely rotate MercadoPago credentials and update the project.

1) Regenerate credentials in MercadoPago dashboard
- Log into your MercadoPago account (sandbox or production as needed).
- Go to Developers / Credentials and regenerate the `access_token` and `public_key` (or create new ones).

2) Update local environment
- Edit your local `.env` (never commit):

```
MERCADOPAGO_ACCESS_TOKEN=NEW_ACCESS_TOKEN
MERCADOPAGO_PUBLIC_KEY=NEW_PUBLIC_KEY
MERCADOPAGO_MODE=sandbox
```

- Restart your application if it caches config.

3) Update CI/CD and hosting secrets
- Update repository secrets (GitHub Actions Secrets, or your CI provider) with the new `MERCADOPAGO_ACCESS_TOKEN` and `MERCADOPAGO_PUBLIC_KEY`.
- Update any server environment variables used by deployment.

4) Validate webhooks and test payments
- If you use webhooks, confirm the webhook signing/URL is unchanged. If MercadoPago rotates webhook secrets separately, update accordingly.
- Run a sandbox payment to verify flows (create a test payment, confirm success, and ensure webhook delivery).

5) Revoke old tokens (if applicable)
- In the MercadoPago dashboard revoke the old token if the platform allows.

6) Confirm removal from Git history
- (Already done) We ran a history purge that removed `.env` and replaced known tokens, but always verify:

```bash
git log --all -S "OLD_ACCESS_TOKEN"
```

7) Notes
- Do not commit the new token. Use secrets in CI and `.env` locally.
- When ready to rotate Stripe, follow the steps in `ROTATION_CHECKLIST.md` or ask me to perform them.
