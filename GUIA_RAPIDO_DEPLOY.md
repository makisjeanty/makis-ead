# 🚀 GUIA RÁPIDO DE DEPLOY - MAKIS EAD

## ⚡ Deploy em 5 Passos

```
┌─────────────────────────────────────────────────────────────┐
│                    MAKIS EAD - DEPLOY                       │
│              De Zero à Produção em 4 Horas                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 PASSO 1: PREPARAR SERVIDOR (30 min)

### 1.1. Instalar Docker

```bash
# Atualizar sistema
sudo apt-get update && sudo apt-get upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo apt-get install docker-compose-plugin

# Verificar
docker --version
docker compose version
```

### 1.2. Configurar Firewall

```bash
# Instalar e configurar UFW
sudo apt-get install ufw
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

✅ **Checkpoint:** Docker instalado e firewall configurado

---

## 📦 PASSO 2: CLONAR E CONFIGURAR (45 min)

### 2.1. Clonar Repositório

```bash
# Criar diretório
sudo mkdir -p /var/www/makis-ead
sudo chown $USER:$USER /var/www/makis-ead

# Clonar
cd /var/www/makis-ead
git clone <URL_DO_REPOSITORIO> .
```

### 2.2. Configurar .env

```bash
# Copiar exemplo
cp .env.example .env

# Editar (use nano ou vim)
nano .env
```

**Variáveis CRÍTICAS para configurar:**

```env
# Aplicação
APP_NAME="Étude Rapide"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

# Banco de Dados
MYSQL_DATABASE=makis_ead_production
MYSQL_USER=makis_ead_user
MYSQL_PASSWORD=SENHA_FORTE_123!@#
MYSQL_ROOT_PASSWORD=ROOT_SENHA_456!@#

# Redis
REDIS_PASSWORD=REDIS_SENHA_789!@#

# Stripe (modo LIVE)
STRIPE_PUBLIC_KEY=pk_live_xxxxxxxx
STRIPE_SECRET_KEY=sk_live_xxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxx
STRIPE_MODE=live

# MercadoPago (modo LIVE)
MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP_USR-xxxxxxxx
MERCADOPAGO_MODE=live

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
```

✅ **Checkpoint:** Código clonado e .env configurado

---

## 🏗️ PASSO 3: FAZER DEPLOY (1 hora)

### 3.1. Executar Script Automatizado

```bash
# Dar permissão
chmod +x deploy.sh

# Executar
./deploy.sh production
```

O script irá:
1. ✅ Fazer backup do banco (se existir)
2. ✅ Verificar variáveis críticas
3. ✅ Parar containers antigos
4. ✅ Fazer pull do código
5. ✅ Build das imagens Docker
6. ✅ Subir containers
7. ✅ Executar migrations
8. ✅ Otimizar cache

### 3.2. Criar Usuário Admin

```bash
docker compose -f docker-compose.prod.yml exec app php artisan make:filament-user
```

Preencha:
- Nome: Seu Nome
- Email: admin@seudominio.com
- Senha: (senha forte)

✅ **Checkpoint:** Aplicação rodando em HTTP

---

## 🔒 PASSO 4: CONFIGURAR SSL (30 min)

### 4.1. Instalar Certbot

```bash
sudo apt-get install certbot python3-certbot-nginx
```

### 4.2. Parar Nginx Temporariamente

```bash
docker compose -f docker-compose.prod.yml stop nginx
```

### 4.3. Obter Certificado

```bash
sudo certbot certonly --standalone -d seudominio.com -d www.seudominio.com
```

### 4.4. Atualizar Nginx Config

Edite `docker/nginx/default.conf`:

```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seudominio.com www.seudominio.com;
    
    ssl_certificate /etc/letsencrypt/live/seudominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seudominio.com/privkey.pem;
    
    # ... resto da configuração
}
```

### 4.5. Atualizar docker-compose.prod.yml

Descomente a linha de volumes SSL no serviço nginx:

```yaml
volumes:
  - /etc/letsencrypt:/etc/letsencrypt:ro
```

### 4.6. Reiniciar Nginx

```bash
docker compose -f docker-compose.prod.yml up -d nginx
```

### 4.7. Configurar Renovação Automática

```bash
sudo crontab -e

# Adicionar:
0 3 * * * certbot renew --quiet --post-hook "docker compose -f /var/www/makis-ead/docker-compose.prod.yml restart nginx"
```

✅ **Checkpoint:** HTTPS funcionando

---

## ✅ PASSO 5: TESTAR E VALIDAR (1 hora)

### 5.1. Verificar Status dos Containers

```bash
docker compose -f docker-compose.prod.yml ps
```

Todos devem estar "Up" e "healthy".

### 5.2. Verificar Logs

```bash
docker compose -f docker-compose.prod.yml logs -f
```

Não deve haver erros críticos.

### 5.3. Testar Endpoints

```bash
# Testar site principal
curl -I https://seudominio.com

# Testar painel admin
curl -I https://seudominio.com/admin

# Testar API Python (interno)
docker compose -f docker-compose.prod.yml exec python_api curl http://localhost:8000/
```

### 5.4. Checklist de Funcionalidades

Acesse o site e teste:

- [ ] Página inicial carrega
- [ ] Listagem de cursos funciona
- [ ] Login/Registro funciona
- [ ] Painel admin acessível (/admin)
- [ ] Pode criar curso no admin
- [ ] Carrinho de compras funciona
- [ ] Checkout funciona
- [ ] Email de confirmação enviado
- [ ] Área do aluno acessível

### 5.5. Configurar Backup Automático

```bash
# Criar script de backup
sudo nano /usr/local/bin/backup-makis-db.sh
```

Cole o conteúdo:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/makis-ead"
DATE=$(date +%Y%m%d_%H%M%S)
CONTAINER="makis_ead_db_prod"

mkdir -p $BACKUP_DIR

DB_NAME=$(grep "^MYSQL_DATABASE=" /var/www/makis-ead/.env | cut -d '=' -f2)
DB_ROOT_PASS=$(grep "^MYSQL_ROOT_PASSWORD=" /var/www/makis-ead/.env | cut -d '=' -f2)

docker exec $CONTAINER mysqldump -u root -p"$DB_ROOT_PASS" "$DB_NAME" > $BACKUP_DIR/backup_$DATE.sql

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "backup_*.sql" -mtime +7 -delete

echo "Backup concluído: backup_$DATE.sql"
```

```bash
# Dar permissão
sudo chmod +x /usr/local/bin/backup-makis-db.sh

# Testar
sudo /usr/local/bin/backup-makis-db.sh

# Adicionar ao cron (diário às 2h)
sudo crontab -e

# Adicionar:
0 2 * * * /usr/local/bin/backup-makis-db.sh >> /var/log/makis-backup.log 2>&1
```

✅ **Checkpoint:** Tudo testado e funcionando!

---

## 🎉 DEPLOY CONCLUÍDO!

```
┌─────────────────────────────────────────────────────────────┐
│                    ✅ DEPLOY COMPLETO!                      │
│                                                             │
│  Seu site está no ar em: https://seudominio.com            │
│  Painel admin: https://seudominio.com/admin                │
│                                                             │
│  Status: PRODUÇÃO                                           │
│  Segurança: SSL/HTTPS ✅                                    │
│  Backup: Automático ✅                                      │
│  Monitoramento: Ativo ✅                                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 MONITORAMENTO DIÁRIO

### Ver Status

```bash
cd /var/www/makis-ead
docker compose -f docker-compose.prod.yml ps
```

### Ver Logs

```bash
# Todos os serviços
docker compose -f docker-compose.prod.yml logs -f --tail=100

# Apenas Laravel
docker compose -f docker-compose.prod.yml logs -f app

# Apenas Nginx
docker compose -f docker-compose.prod.yml logs -f nginx
```

### Ver Recursos

```bash
docker stats
```

### Ver Espaço em Disco

```bash
df -h
```

---

## 🔄 ATUALIZAÇÃO FUTURA

Quando precisar atualizar:

```bash
cd /var/www/makis-ead
./deploy.sh production
```

O script fará tudo automaticamente!

---

## 🆘 PROBLEMAS COMUNS

### Site não carrega

```bash
# Verificar logs
docker compose -f docker-compose.prod.yml logs nginx

# Reiniciar nginx
docker compose -f docker-compose.prod.yml restart nginx
```

### Erro 500

```bash
# Ver logs do Laravel
docker compose -f docker-compose.prod.yml logs app

# Limpar cache
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker compose -f docker-compose.prod.yml exec app php artisan config:clear
```

### Banco de dados não conecta

```bash
# Verificar se MySQL está rodando
docker compose -f docker-compose.prod.yml ps db

# Ver logs do MySQL
docker compose -f docker-compose.prod.yml logs db

# Reiniciar MySQL
docker compose -f docker-compose.prod.yml restart db
```

### Containers não iniciam

```bash
# Rebuild completo
docker compose -f docker-compose.prod.yml down
docker compose -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.prod.yml up -d
```

---

## 📞 SUPORTE

Se precisar de ajuda:

1. Consulte `DEPLOY_CHECKLIST.md` (guia completo)
2. Consulte `ANALISE_FINAL.md` (arquitetura)
3. Verifique logs: `docker compose -f docker-compose.prod.yml logs`

---

## ✅ CHECKLIST FINAL

- [ ] Servidor preparado (Docker, Firewall)
- [ ] Código clonado
- [ ] .env configurado
- [ ] Deploy executado
- [ ] Usuário admin criado
- [ ] SSL/HTTPS configurado
- [ ] Backup automático configurado
- [ ] Site testado e funcionando
- [ ] Painel admin acessível
- [ ] Pagamentos testados
- [ ] Monitoramento ativo

---

**Tempo total estimado: 3-4 horas**

**Dificuldade: Média** (com este guia, fica fácil!)

**Status: ✅ PRONTO PARA PRODUÇÃO**

---

**Boa sorte! 🚀**

*Para mais detalhes, consulte DEPLOY_CHECKLIST.md*
