#!/bin/bash

# ===================================
# MAKIS EAD - Script de Deploy Automatizado
# ===================================
# Este script automatiza o processo de deploy em produção
# Uso: ./deploy.sh [ambiente]
# Exemplo: ./deploy.sh production

set -e  # Parar em caso de erro

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funções de log
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Banner
echo "========================================="
echo "   MAKIS EAD - Deploy Automatizado"
echo "========================================="
echo ""

# Verificar se está rodando como root ou com sudo
if [ "$EUID" -eq 0 ]; then 
    log_warning "Não execute este script como root!"
    exit 1
fi

# Verificar ambiente
ENVIRONMENT=${1:-production}
log_info "Ambiente: $ENVIRONMENT"

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    log_error "Docker não está instalado!"
    exit 1
fi

if ! command -v docker compose &> /dev/null; then
    log_error "Docker Compose não está instalado!"
    exit 1
fi

log_success "Docker e Docker Compose encontrados"

# Verificar se arquivo .env existe
if [ ! -f .env ]; then
    log_error "Arquivo .env não encontrado!"
    log_info "Copie .env.example para .env e configure as variáveis"
    exit 1
fi

log_success "Arquivo .env encontrado"

# Verificar variáveis críticas no .env
log_info "Verificando variáveis críticas..."

check_env_var() {
    if ! grep -q "^$1=" .env || grep -q "^$1=$" .env || grep -q "^$1=changeme" .env; then
        log_error "Variável $1 não configurada ou com valor padrão!"
        return 1
    fi
    return 0
}

CRITICAL_VARS=(
    "APP_KEY"
    "DB_DATABASE"
    "DB_USERNAME"
    "DB_PASSWORD"
    "MYSQL_ROOT_PASSWORD"
)

ALL_VARS_OK=true
for var in "${CRITICAL_VARS[@]}"; do
    if ! check_env_var "$var"; then
        ALL_VARS_OK=false
    fi
done

if [ "$ALL_VARS_OK" = false ]; then
    log_error "Configure todas as variáveis críticas antes de continuar!"
    exit 1
fi

log_success "Variáveis críticas configuradas"

# Perguntar confirmação
echo ""
log_warning "ATENÇÃO: Este script irá:"
echo "  1. Fazer backup do banco de dados"
echo "  2. Parar os containers atuais"
echo "  3. Fazer pull do código mais recente"
echo "  4. Rebuild das imagens Docker"
echo "  5. Subir os novos containers"
echo "  6. Executar migrations"
echo "  7. Limpar e recriar cache"
echo ""
read -p "Deseja continuar? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    log_info "Deploy cancelado pelo usuário"
    exit 0
fi

# ===================================
# ETAPA 1: Backup do Banco de Dados
# ===================================
log_info "ETAPA 1/8: Fazendo backup do banco de dados..."

BACKUP_DIR="./backups"
mkdir -p $BACKUP_DIR

BACKUP_FILE="$BACKUP_DIR/backup_$(date +%Y%m%d_%H%M%S).sql"

if docker ps | grep -q makis_ead_db_prod; then
    DB_CONTAINER="makis_ead_db_prod"
    DB_NAME=$(grep "^MYSQL_DATABASE=" .env | cut -d '=' -f2)
    DB_ROOT_PASS=$(grep "^MYSQL_ROOT_PASSWORD=" .env | cut -d '=' -f2)
    
    docker exec $DB_CONTAINER mysqldump -u root -p"$DB_ROOT_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null || {
        log_warning "Não foi possível fazer backup (container pode não estar rodando)"
    }
    
    if [ -f "$BACKUP_FILE" ]; then
        log_success "Backup criado: $BACKUP_FILE"
    fi
else
    log_warning "Container do banco não está rodando, pulando backup"
fi

# ===================================
# ETAPA 2: Parar Containers
# ===================================
log_info "ETAPA 2/8: Parando containers atuais..."

docker compose -f docker-compose.prod.yml down || {
    log_warning "Nenhum container estava rodando"
}

log_success "Containers parados"

# ===================================
# ETAPA 3: Pull do Código
# ===================================
log_info "ETAPA 3/8: Atualizando código..."

# Verificar se é um repositório git
if [ -d .git ]; then
    git pull origin main || git pull origin master || {
        log_warning "Não foi possível fazer pull do repositório"
    }
    log_success "Código atualizado"
else
    log_warning "Não é um repositório Git, pulando pull"
fi

# ===================================
# ETAPA 4: Build das Imagens
# ===================================
log_info "ETAPA 4/8: Buildando imagens Docker..."

docker compose -f docker-compose.prod.yml build --no-cache

log_success "Imagens buildadas com sucesso"

# ===================================
# ETAPA 5: Subir Containers
# ===================================
log_info "ETAPA 5/8: Subindo containers..."

docker compose -f docker-compose.prod.yml up -d

log_success "Containers iniciados"

# Aguardar containers ficarem saudáveis
log_info "Aguardando containers ficarem prontos..."
sleep 30

# ===================================
# ETAPA 6: Executar Migrations
# ===================================
log_info "ETAPA 6/8: Executando migrations..."

docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

log_success "Migrations executadas"

# ===================================
# ETAPA 7: Otimizar Cache
# ===================================
log_info "ETAPA 7/8: Otimizando cache..."

# Limpar cache antigo
docker compose -f docker-compose.prod.yml exec -T app php artisan config:clear
docker compose -f docker-compose.prod.yml exec -T app php artisan cache:clear
docker compose -f docker-compose.prod.yml exec -T app php artisan view:clear
docker compose -f docker-compose.prod.yml exec -T app php artisan route:clear

# Recriar cache otimizado
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Otimizar autoloader
docker compose -f docker-compose.prod.yml exec -T app composer dump-autoload --optimize

log_success "Cache otimizado"

# ===================================
# ETAPA 8: Verificação Final
# ===================================
log_info "ETAPA 8/8: Verificando status dos containers..."

docker compose -f docker-compose.prod.yml ps

echo ""
log_success "========================================="
log_success "   DEPLOY CONCLUÍDO COM SUCESSO!"
log_success "========================================="
echo ""

log_info "Próximos passos:"
echo "  1. Verifique os logs: docker compose -f docker-compose.prod.yml logs -f"
echo "  2. Teste o site no navegador"
echo "  3. Verifique o painel admin: /admin"
echo ""

log_info "Comandos úteis:"
echo "  - Ver logs: docker compose -f docker-compose.prod.yml logs -f"
echo "  - Reiniciar: docker compose -f docker-compose.prod.yml restart"
echo "  - Parar: docker compose -f docker-compose.prod.yml down"
echo "  - Status: docker compose -f docker-compose.prod.yml ps"
echo ""

# Mostrar informações de backup
if [ -f "$BACKUP_FILE" ]; then
    log_info "Backup salvo em: $BACKUP_FILE"
    log_info "Para restaurar: docker exec -i makis_ead_db_prod mysql -u root -p\$MYSQL_ROOT_PASSWORD \$MYSQL_DATABASE < $BACKUP_FILE"
fi

exit 0
