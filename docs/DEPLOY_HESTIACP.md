# 🚀 PLANO DE DEPLOY - HESTIACP

**VPS:** 195.26.252.210  
**Domínio:** etuderapide.com  
**Painel:** HestiaCP  
**Usuário:** ETUDE-RAPIDE  
**Caminho:** /home/ETUDE-RAPIDE/web/etuderapide.com/

---

## ✅ SITUAÇÃO ATUAL

- ✅ HestiaCP instalado e configurado
- ✅ Domínio etuderapide.com configurado
- ✅ SSL/HTTPS funcionando
- ✅ PHP 8.3 instalado
- ✅ Nginx rodando
- ❌ Site atual com erro 500

---

## 🎯 ESTRATÉGIA DE DEPLOY (SEM DOCKER)

Como temos HestiaCP, vamos fazer deploy **tradicional** (sem Docker):

### Vantagens
- ✅ Usa infraestrutura já existente
- ✅ Mais rápido
- ✅ Sem necessidade de instalar Docker
- ✅ Gerenciamento pelo painel HestiaCP

### Desvantagens
- ⚠️ Não usa a arquitetura Docker que preparamos
- ⚠️ API Python precisará rodar separadamente

---

## 📋 PASSOS DO DEPLOY

### 1. Preparação (10 min)

```bash
# Conectar ao VPS
ssh root@195.26.252.210

# Ir para o diretório do site
cd /home/ETUDE-RAPIDE/web/etuderapide.com/

# Fazer backup do conteúdo atual
tar -czf ~/backup_etuderapide_$(date +%Y%m%d_%H%M%S).tar.gz public_html/

# Limpar public_html
rm -rf public_html/*
```

### 2. Clonar Repositório (5 min)

```bash
# Clonar para diretório temporário
cd /home/ETUDE-RAPIDE/
git clone <URL_DO_REPOSITORIO> makis-ead-temp

# Mover conteúdo para public_html
mv makis-ead-temp/* /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/
mv makis-ead-temp/.* /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/ 2>/dev/null || true

# Remover temp
rm -rf makis-ead-temp
```

### 3. Configurar .env (10 min)

```bash
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/

# Copiar .env.example
cp .env.example .env

# Editar .env
nano .env
```

**Configurações importantes:**

```env
APP_NAME="Étude Rapide"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://etuderapide.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ETUDE-RAPIDE_makis_ead
DB_USERNAME=ETUDE-RAPIDE_makis_user
DB_PASSWORD=SENHA_FORTE_AQUI

# Resto das configurações...
```

### 4. Criar Banco de Dados via HestiaCP (5 min)

**Opção A: Via Painel Web**
1. Acessar HestiaCP: https://195.26.252.210:8083
2. Login com usuário ETUDE-RAPIDE
3. Databases → Add Database
4. Nome: makis_ead
5. Criar usuário: makis_user
6. Anotar senha gerada

**Opção B: Via CLI**

```bash
# Como root
v-add-database ETUDE-RAPIDE makis_ead makis_user SENHA_FORTE
```

### 5. Instalar Dependências (15 min)

```bash
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/

# Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# Instalar dependências Node
npm install
npm run build

# Gerar chave da aplicação
php artisan key:generate

# Criar link de storage
php artisan storage:link
```

### 6. Executar Migrations (5 min)

```bash
# Executar migrations
php artisan migrate --force

# Executar seeders (se necessário)
php artisan db:seed --force
```

### 7. Configurar Permissões (5 min)

```bash
# Ajustar ownership
chown -R ETUDE-RAPIDE:ETUDE-RAPIDE /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/

# Permissões de storage e cache
chmod -R 775 /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/storage
chmod -R 775 /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/bootstrap/cache
```

### 8. Configurar Nginx (10 min)

O HestiaCP já gerencia o Nginx, mas precisamos ajustar para Laravel:

```bash
# Editar configuração do Nginx para o domínio
nano /home/ETUDE-RAPIDE/conf/web/etuderapide.com/nginx.conf
```

Adicionar antes do `location /`:

```nginx
# Laravel configuration
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

Reiniciar Nginx:

```bash
systemctl reload nginx
```

### 9. Otimizar Cache (5 min)

```bash
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Recriar cache otimizado
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Criar Usuário Admin (5 min)

```bash
php artisan make:filament-user
```

---

## 🐍 API PYTHON (OPCIONAL)

Para a API Python de gamificação, temos duas opções:

### Opção 1: Rodar como Serviço Systemd

```bash
# Instalar Python e dependências
apt-get install python3-pip python3-venv

# Criar ambiente virtual
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/python_api/
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt

# Criar serviço systemd
nano /etc/systemd/system/makis-python-api.service
```

Conteúdo:

```ini
[Unit]
Description=Makis EAD Python API
After=network.target

[Service]
Type=simple
User=ETUDE-RAPIDE
WorkingDirectory=/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/python_api
Environment="PATH=/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/python_api/venv/bin"
ExecStart=/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/python_api/venv/bin/uvicorn main:app --host 127.0.0.1 --port 8001
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
# Ativar serviço
systemctl daemon-reload
systemctl enable makis-python-api
systemctl start makis-python-api
```

### Opção 2: Desabilitar Temporariamente

Se não for usar a gamificação agora, pode comentar as rotas da API Python no código.

---

## ✅ CHECKLIST DE DEPLOY

- [ ] Backup do site atual
- [ ] Clonar repositório
- [ ] Configurar .env
- [ ] Criar banco de dados
- [ ] Instalar dependências (Composer + NPM)
- [ ] Gerar APP_KEY
- [ ] Executar migrations
- [ ] Ajustar permissões
- [ ] Configurar Nginx
- [ ] Otimizar cache
- [ ] Criar usuário admin
- [ ] Testar site
- [ ] (Opcional) Configurar API Python

---

## 🔧 COMANDOS RÁPIDOS

### Deploy Completo (Copiar e Colar)

```bash
# 1. Backup
cd /home/ETUDE-RAPIDE/web/etuderapide.com/
tar -czf ~/backup_etuderapide_$(date +%Y%m%d_%H%M%S).tar.gz public_html/

# 2. Preparar diretório
cd /home/ETUDE-RAPIDE/
# (Aqui você faz o upload via Git ou FTP)

# 3. Configurar
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/
cp .env.example .env
nano .env  # Configurar variáveis

# 4. Instalar
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
php artisan storage:link

# 5. Banco de dados
php artisan migrate --force
php artisan db:seed --force

# 6. Permissões
chown -R ETUDE-RAPIDE:ETUDE-RAPIDE /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/
chmod -R 775 storage bootstrap/cache

# 7. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Admin
php artisan make:filament-user

# 9. Reiniciar
systemctl reload nginx
systemctl reload php8.3-fpm
```

---

## 📞 PRÓXIMO PASSO

**Posso começar o deploy agora?**

Preciso que você me confirme:
1. Tem o repositório Git configurado? (URL do repositório)
2. Ou prefere que eu faça upload manual dos arquivos?
3. Quer que eu configure a API Python também ou deixamos para depois?

---

**Tempo estimado total: 1-1.5 horas**
