# 🚀 COMO ATUALIZAR A VERSÃO ONLINE

Acabei de enviar todas as atualizações locais para o repositório remoto (GitHub).
Para aplicar essas mudanças no servidor de produção, siga estes passos:

## 1. Conecte-se ao servidor via SSH

```bash
ssh root@195.26.252.210
```

## 2. Navegue até a pasta do projeto

```bash
cd /home/ETUDE-RAPIDE/web/etuderapide.com/public_html
```

## 3. Baixe as atualizações

```bash
git pull origin main
```

## 4. Atualize o ambiente (Opção Automática)

Tente rodar o script de deploy:

```bash
./deploy.sh production
```

## 5. Atualize o ambiente (Opção Manual - Caso o script falhe)

Se o script acima der erro (como mencionado nos logs anteriores), rode estes comandos manualmente:

```bash
# 1. Parar containers (para garantir rebuild)
docker compose -f docker-compose.prod.yml down

# 2. Rebuild dos containers
docker compose -f docker-compose.prod.yml up -d --build

# 3. Rodar Migrations (Atualizar Banco de Dados)
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 4. Limpar e Recriar Cache
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan optimize

# 5. Reiniciar Filas (se necessário)
docker compose -f docker-compose.prod.yml restart app
```

## ✅ Verificação

Acesse https://etuderapide.com e verifique se as alterações estão visíveis.
