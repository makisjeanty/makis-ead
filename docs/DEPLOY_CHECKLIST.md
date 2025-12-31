# 🚀 CHECKLIST COMPLETO DE DEPLOY - MAKIS EAD

## 📋 RESUMO
Este documento contém todos os passos necessários para preparar e fazer o deploy da aplicação **Makis EAD** em ambiente de produção.

**Data de Criação:** 2025-12-28  
**Status:** ✅ PRONTO PARA DEPLOY

---

## 🎯 PRÉ-REQUISITOS

### Servidor/VPS
- [ ] Ubuntu 20.04+ ou Debian 11+
- [ ] Mínimo 2GB RAM (Recomendado: 4GB+)
- [ ] Mínimo 20GB de disco
- [ ] Acesso SSH configurado
- [ ] Domínio configurado (DNS apontando para o servidor)

### Software Necessário
- [ ] Docker Engine 24.0+
- [ ] Docker Compose 2.0+
- [ ] Git
- [ ] Certbot (para SSL/HTTPS)

---

## 📦 ETAPA 1: PREPARAÇÃO DO AMBIENTE

### 1.1. Instalar Docker e Docker Compose

```bash
# Atualizar sistema
sudo apt-get update && sudo apt-get upgrade -y

# Instalar dependências
sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common

# Adicionar repositório Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Instalar Docker
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Verificar instalação
docker --version
docker compose version

# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER
newgrp docker
```

### 1.2. Instalar Git

```bash
sudo apt-get install -y git
git --version
```

### 1.3. Clonar Repositório

```bash
# Criar diretório para aplicação
sudo mkdir -p /var/www/makis-ead
sudo chown $USER:$USER /var/www/makis-ead
cd /var/www/makis-ead

# Clonar repositório (ajuste a URL)
git clone <URL_DO_REPOSITORIO> .
```

---

## 🔐 ETAPA 2: CONFIGURAÇÃO DE VARIÁVEIS DE AMBIENTE

### 2.1. Criar arquivo .env

```bash
cd /var/www/makis-ead
cp .env.example .env
nano .env
```

### 2.2. Configurar Variáveis Críticas

**IMPORTANTE:** Preencha TODAS as variáveis abaixo com valores reais de produção:

```env
# === APLICAÇÃO ===
APP_NAME="Étude Rapide"
APP_ENV=production
APP_KEY=                          # Será gerado depois
APP_DEBUG=false                   # SEMPRE false em produção
APP_TIMEZONE=America/Port-au-Prince
APP_URL=https://seudominio.com    # Seu domínio real
APP_LOCALE=fr

# === BANCO DE DADOS ===
DB_CONNECTION=mysql
DB_HOST=db                        # Nome do serviço Docker
DB_PORT=3306
DB_DATABASE=makis_ead_production
DB_USERNAME=makis_ead_user
DB_PASSWORD=SENHA_FORTE_AQUI_123!@#  # MUDE ISSO!

# === MYSQL (Docker) ===
MYSQL_DATABASE=makis_ead_production
MYSQL_USER=makis_ead_user
MYSQL_PASSWORD=SENHA_FORTE_AQUI_123!@#  # MESMA SENHA ACIMA
MYSQL_ROOT_PASSWORD=ROOT_SENHA_FORTE_456!@#  # SENHA ROOT

# === CACHE & SESSION ===
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379

# === MOEDA ===
DEFAULT_CURRENCY=HTG
CURRENCY_API_PROVIDER=exchangerate-api
CURRENCY_API_KEY=sua-chave-api-aqui

# === MERCADO PAGO ===
MERCADOPAGO_PUBLIC_KEY=APP_USR-xxxxxxxx
MERCADOPAGO_ACCESS_TOKEN=APP_USR-xxxxxxxx
MERCADOPAGO_MODE=live              # 'live' para produção

# === STRIPE ===
STRIPE_PUBLIC_KEY=pk_live_xxxxxxxx
STRIPE_SECRET_KEY=sk_live_xxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxx
STRIPE_MODE=live                   # 'live' para produção

# Stripe Price IDs (criar no Dashboard Stripe)
STRIPE_STARTER_PRICE_ID=price_xxxxxxxx
STRIPE_PROFESSIONAL_PRICE_ID=price_xxxxxxxx
STRIPE_ENTERPRISE_PRICE_ID=price_xxxxxxxx
STRIPE_TRIAL_DAYS=0

# === EMAIL ===
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com          # Ou seu provedor SMTP
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME="${APP_NAME}"

# === AWS S3 (Opcional - para armazenamento de arquivos) ===
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# === ANALYTICS ===
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
FACEBOOK_PIXEL_ID=

# === SEO ===
SITE_DESCRIPTION="Plateforme d'apprentissage en ligne pour la francophonie"
SITE_KEYWORDS="cours en ligne, formation, éducation, Haiti, français"
```

### 2.3. Checklist de Segurança .env
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] Senhas fortes e únicas
- [ ] Credenciais de pagamento em modo 'live'
- [ ] APP_URL com domínio real
- [ ] Arquivo .env com permissões 600

```bash
chmod 600 .env
```

---

## 🏗️ ETAPA 3: BUILD E OTIMIZAÇÃO

### 3.1. Melhorar Dockerfile para Produção

O Dockerfile atual já está bom, mas vamos garantir otimizações:

```bash
# Verificar se o Dockerfile está correto
cat Dockerfile
```

### 3.2. Build das Imagens Docker

```bash
cd /var/www/makis-ead

# Build com docker-compose (produção)
docker compose -f docker-compose.prod.yml build --no-cache

# Verificar imagens criadas
docker images | grep makis
```

---

## 🚀 ETAPA 4: INICIALIZAÇÃO DOS SERVIÇOS

### 4.1. Subir Containers

```bash
# Subir todos os serviços em background
docker compose -f docker-compose.prod.yml up -d

# Verificar status
docker compose -f docker-compose.prod.yml ps

# Ver logs em tempo real
docker compose -f docker-compose.prod.yml logs -f
```

### 4.2. Gerar APP_KEY

```bash
# Entrar no container da aplicação
docker compose -f docker-compose.prod.yml exec app bash

# Gerar chave
php artisan key:generate

# Sair do container
exit
```

### 4.3. Executar Migrations

```bash
# Executar migrations
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Executar seeders (CUIDADO: só na primeira vez)
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --force
```

### 4.4. Criar Link de Storage

```bash
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
```

### 4.5. Otimizar Cache

```bash
# Cache de configuração
docker compose -f docker-compose.prod.yml exec app php artisan config:cache

# Cache de rotas
docker compose -f docker-compose.prod.yml exec app php artisan route:cache

# Cache de views
docker compose -f docker-compose.prod.yml exec app php artisan view:cache

# Otimizar autoloader
docker compose -f docker-compose.prod.yml exec app composer dump-autoload --optimize
```

### 4.6. Criar Usuário Admin Filament

```bash
docker compose -f docker-compose.prod.yml exec app php artisan make:filament-user
```

---

## 🔒 ETAPA 5: CONFIGURAÇÃO SSL/HTTPS

### 5.1. Instalar Certbot

```bash
sudo apt-get install -y certbot python3-certbot-nginx
```

### 5.2. Atualizar Nginx para Produção

Criar arquivo `docker/nginx/production.conf`:

```nginx
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    
    # Redirecionar HTTP para HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seudominio.com www.seudominio.com;
    
    # Certificados SSL (serão gerados pelo Certbot)
    ssl_certificate /etc/letsencrypt/live/seudominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seudominio.com/privkey.pem;
    
    # Configurações SSL
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    index index.php index.html;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /var/www/public;
    
    # Aumentar tamanho máximo de upload
    client_max_body_size 100M;
    
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_read_timeout 300;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
        gzip_static on;
    }
    
    # Cache de assets estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 5.3. Obter Certificado SSL

```bash
# Parar Nginx temporariamente
docker compose -f docker-compose.prod.yml stop nginx

# Obter certificado
sudo certbot certonly --standalone -d seudominio.com -d www.seudominio.com

# Reiniciar Nginx
docker compose -f docker-compose.prod.yml start nginx
```

### 5.4. Renovação Automática SSL

```bash
# Testar renovação
sudo certbot renew --dry-run

# Adicionar ao cron para renovação automática
sudo crontab -e

# Adicionar linha:
0 3 * * * certbot renew --quiet --post-hook "docker compose -f /var/www/makis-ead/docker-compose.prod.yml restart nginx"
```

---

## 🔍 ETAPA 6: TESTES E VALIDAÇÃO

### 6.1. Verificar Serviços

```bash
# Status dos containers
docker compose -f docker-compose.prod.yml ps

# Logs de cada serviço
docker compose -f docker-compose.prod.yml logs app
docker compose -f docker-compose.prod.yml logs nginx
docker compose -f docker-compose.prod.yml logs db
docker compose -f docker-compose.prod.yml logs python_api
```

### 6.2. Testar Endpoints

```bash
# Testar aplicação principal
curl -I https://seudominio.com

# Testar API Python
curl https://seudominio.com:8001/

# Testar Filament Admin
curl -I https://seudominio.com/admin
```

### 6.3. Checklist de Funcionalidades
- [ ] Página inicial carrega corretamente
- [ ] Login/Registro funcionando
- [ ] Painel Filament acessível (/admin)
- [ ] Listagem de cursos funcional
- [ ] Carrinho de compras operacional
- [ ] Checkout com Stripe/MercadoPago
- [ ] API Python respondendo
- [ ] Emails sendo enviados
- [ ] SSL/HTTPS ativo

---

## 📊 ETAPA 7: MONITORAMENTO E BACKUP

### 7.1. Configurar Logs

```bash
# Ver logs em tempo real
docker compose -f docker-compose.prod.yml logs -f --tail=100

# Logs do Laravel
docker compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log
```

### 7.2. Backup do Banco de Dados

```bash
# Criar script de backup
sudo nano /usr/local/bin/backup-makis-db.sh
```

Conteúdo do script:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/makis-ead"
DATE=$(date +%Y%m%d_%H%M%S)
CONTAINER="makis_ead_db_prod"

mkdir -p $BACKUP_DIR

docker exec $CONTAINER mysqldump -u root -p$MYSQL_ROOT_PASSWORD makis_ead_production > $BACKUP_DIR/backup_$DATE.sql

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "backup_*.sql" -mtime +7 -delete

echo "Backup concluído: backup_$DATE.sql"
```

```bash
# Dar permissão de execução
sudo chmod +x /usr/local/bin/backup-makis-db.sh

# Adicionar ao cron (diário às 2h)
sudo crontab -e
# Adicionar:
0 2 * * * /usr/local/bin/backup-makis-db.sh >> /var/log/makis-backup.log 2>&1
```

### 7.3. Monitoramento de Recursos

```bash
# Ver uso de recursos dos containers
docker stats

# Ver espaço em disco
df -h

# Ver uso de memória
free -h
```

---

## 🔄 ETAPA 8: ATUALIZAÇÕES E MANUTENÇÃO

### 8.1. Processo de Atualização

```bash
cd /var/www/makis-ead

# Fazer backup antes de atualizar
/usr/local/bin/backup-makis-db.sh

# Puxar últimas alterações
git pull origin main

# Rebuild e restart
docker compose -f docker-compose.prod.yml down
docker compose -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.prod.yml up -d

# Executar migrations
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Limpar e recriar cache
docker compose -f docker-compose.prod.yml exec app php artisan config:clear
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### 8.2. Comandos Úteis de Manutenção

```bash
# Reiniciar todos os serviços
docker compose -f docker-compose.prod.yml restart

# Reiniciar serviço específico
docker compose -f docker-compose.prod.yml restart app

# Ver logs de erro
docker compose -f docker-compose.prod.yml logs --tail=50 app | grep ERROR

# Limpar containers parados
docker system prune -a

# Entrar no container para debug
docker compose -f docker-compose.prod.yml exec app bash
```

---

## 🛡️ ETAPA 9: SEGURANÇA

### 9.1. Firewall

```bash
# Instalar UFW
sudo apt-get install -y ufw

# Configurar regras
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Ativar firewall
sudo ufw enable

# Verificar status
sudo ufw status
```

### 9.2. Fail2Ban (Proteção contra ataques)

```bash
# Instalar
sudo apt-get install -y fail2ban

# Configurar
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local

# Iniciar serviço
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 9.3. Checklist de Segurança
- [ ] Firewall configurado (UFW)
- [ ] Fail2Ban ativo
- [ ] SSL/HTTPS funcionando
- [ ] Senhas fortes em .env
- [ ] Porta MySQL não exposta publicamente
- [ ] APP_DEBUG=false
- [ ] Backups automáticos configurados
- [ ] Atualizações de segurança do sistema

---

## 📈 ETAPA 10: OTIMIZAÇÃO DE PERFORMANCE

### 10.1. Adicionar Redis (Cache)

Atualizar `docker-compose.prod.yml` para incluir Redis:

```yaml
services:
  # ... outros serviços ...
  
  redis:
    image: redis:7-alpine
    container_name: makis_ead_redis_prod
    restart: unless-stopped
    volumes:
      - redis_data:/data

volumes:
  dbdata:
  redis_data:
```

### 10.2. Configurar Queue Workers

```bash
# Criar serviço systemd para queue worker
sudo nano /etc/systemd/system/makis-queue.service
```

Conteúdo:

```ini
[Unit]
Description=Makis EAD Queue Worker
After=docker.service
Requires=docker.service

[Service]
Type=simple
User=root
WorkingDirectory=/var/www/makis-ead
ExecStart=/usr/bin/docker compose -f docker-compose.prod.yml exec -T app php artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
# Ativar serviço
sudo systemctl enable makis-queue
sudo systemctl start makis-queue
sudo systemctl status makis-queue
```

---

## ✅ CHECKLIST FINAL DE DEPLOY

### Pré-Deploy
- [ ] Servidor/VPS provisionado
- [ ] Docker e Docker Compose instalados
- [ ] Domínio configurado (DNS)
- [ ] Repositório clonado
- [ ] .env configurado com valores de produção
- [ ] Credenciais de pagamento (Stripe/MercadoPago) em modo live

### Deploy
- [ ] Imagens Docker buildadas
- [ ] Containers iniciados
- [ ] APP_KEY gerada
- [ ] Migrations executadas
- [ ] Seeders executados (primeira vez)
- [ ] Storage linkado
- [ ] Cache otimizado
- [ ] Usuário admin criado

### Segurança
- [ ] SSL/HTTPS configurado
- [ ] Firewall ativo
- [ ] Fail2Ban configurado
- [ ] Senhas fortes
- [ ] APP_DEBUG=false
- [ ] Porta MySQL não exposta

### Monitoramento
- [ ] Backup automático configurado
- [ ] Logs funcionando
- [ ] Renovação SSL automática
- [ ] Monitoramento de recursos

### Testes
- [ ] Site acessível via HTTPS
- [ ] Login/Registro funcionando
- [ ] Painel admin acessível
- [ ] Pagamentos testados
- [ ] Emails sendo enviados
- [ ] API Python respondendo

---

## 🆘 TROUBLESHOOTING

### Problema: Containers não iniciam

```bash
# Ver logs detalhados
docker compose -f docker-compose.prod.yml logs

# Verificar .env
cat .env | grep -v "^#" | grep -v "^$"

# Rebuild completo
docker compose -f docker-compose.prod.yml down -v
docker compose -f docker-compose.prod.yml build --no-cache
docker compose -f docker-compose.prod.yml up -d
```

### Problema: Erro de permissões

```bash
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/storage
docker compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/storage
```

### Problema: Migrations falham

```bash
# Verificar conexão com banco
docker compose -f docker-compose.prod.yml exec app php artisan tinker
# Dentro do tinker:
DB::connection()->getPdo();

# Resetar migrations (CUIDADO!)
docker compose -f docker-compose.prod.yml exec app php artisan migrate:fresh --force
```

### Problema: Site lento

```bash
# Limpar cache
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker compose -f docker-compose.prod.yml exec app php artisan config:clear
docker compose -f docker-compose.prod.yml exec app php artisan view:clear

# Recriar cache otimizado
docker compose -f docker-compose.prod.yml exec app php artisan config:cache
docker compose -f docker-compose.prod.yml exec app php artisan route:cache
docker compose -f docker-compose.prod.yml exec app php artisan view:cache
```

---

## 📞 SUPORTE

Para problemas ou dúvidas:
1. Verificar logs: `docker compose -f docker-compose.prod.yml logs`
2. Consultar documentação Laravel: https://laravel.com/docs
3. Consultar documentação Filament: https://filamentphp.com/docs

---

**Última Atualização:** 2025-12-28  
**Versão:** 1.0  
**Status:** ✅ PRONTO PARA PRODUÇÃO
