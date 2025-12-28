#!/bin/bash

# MAKIS EAD - SCRIPT DE CONFIGURAÇÃO AUTOMÁTICA DO AMBIENTE
# ========================================================

set -e  # Parar em caso de erro

echo "🚀 INICIANDO CONFIGURAÇÃO DO MAKIS EAD"
echo "========================================"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
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

# Verificar dependências do sistema
check_dependencies() {
    log_info "Verificando dependências do sistema..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker não encontrado. Instale o Docker primeiro."
        exit 1
    fi
    
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose não encontrado. Instale o Docker Compose primeiro."
        exit 1
    fi
    
    log_success "Dependências verificadas!"
}

# Configurar arquivo .env
setup_environment() {
    log_info "Configurando arquivo de ambiente..."
    
    if [ ! -f .env ]; then
        cp .env.example .env
        log_success "Arquivo .env criado a partir do .env.example"
    else
        log_warning "Arquivo .env já existe. Mantendo configuração existente."
    fi
    
    # Gerar chave da aplicação Laravel
    if command -v php &> /dev/null; then
        log_info "Gerando chave da aplicação Laravel..."
        php artisan key:generate --force || log_warning "Não foi possível gerar a chave. Execute manualmente: php artisan key:generate"
    else
        log_warning "PHP não encontrado. Configure a chave manualmente no .env"
    fi
}

# Instalar dependências PHP
install_php_dependencies() {
    log_info "Instalando dependências PHP..."
    
    if command -v composer &> /dev/null; then
        composer install --optimize-autoloader --no-dev
        log_success "Dependências PHP instaladas!"
    else
        log_warning "Composer não encontrado. Instale as dependências manualmente: composer install"
    fi
}

# Instalar dependências Node.js
install_node_dependencies() {
    log_info "Instalando dependências Node.js..."
    
    if command -v npm &> /dev/null; then
        npm install
        log_success "Dependências Node.js instaladas!"
        
        # Build dos assets
        if [ -f "vite.config.js" ]; then
            log_info "Buildando assets..."
            npm run build
            log_success "Assets buildados!"
        fi
    else
        log_warning "NPM não encontrado. Instale as dependências manualmente: npm install"
    fi
}

# Configurar banco de dados
setup_database() {
    log_info "Configurando banco de dados..."
    
    # Verificar se os serviços Docker estão rodando
    if docker-compose ps | grep -q "Up"; then
        log_info "Serviços Docker encontrados. Aguardando inicialização..."
        sleep 10
        
        # Executar migrations
        if command -v php &> /dev/null; then
            log_info "Executando migrations..."
            php artisan migrate --force || log_warning "Erro ao executar migrations"
            
            # Executar seeders
            log_info "Executando seeders..."
            php artisan db:seed --force || log_warning "Erro ao executar seeders"
            
            # Criar usuário admin
            log_info "Criando usuário admin Filament..."
            echo "y" | php artisan make:filament-user || log_warning "Erro ao criar usuário admin"
        else
            log_warning "PHP não encontrado. Execute migrations manualmente: php artisan migrate"
        fi
    else
        log_warning "Serviços Docker não estão rodando. Inicie com: docker-compose up -d"
    fi
}

# Iniciar serviços Docker
start_docker_services() {
    log_info "Iniciando serviços Docker..."
    
    # Parar serviços existentes
    docker-compose down
    
    # Construir e iniciar serviços
    docker-compose up -d --build
    
    log_success "Serviços Docker iniciados!"
    
    # Mostrar status
    log_info "Status dos serviços:"
    docker-compose ps
}

# Verificar serviços
verify_services() {
    log_info "Verificando serviços..."
    
    sleep 5  # Aguardar inicialização
    
    # Verificar Laravel
    if curl -s http://localhost:8000 > /dev/null; then
        log_success "✅ Laravel (porta 8000) - OK"
    else
        log_warning "❌ Laravel (porta 8000) - Não respondendo"
    fi
    
    # Verificar Python API
    if curl -s http://localhost:8001/ > /dev/null; then
        log_success "✅ Python API (porta 8001) - OK"
    else
        log_warning "❌ Python API (porta 8001) - Não respondendo"
    fi
    
    # Verificar MySQL
    if docker-compose exec -T db mysql -u makis_ead_user -padmin@123456 -e "SELECT 1" makis_ead_db &> /dev/null; then
        log_success "✅ MySQL (porta 3306) - OK"
    else
        log_warning "❌ MySQL (porta 3306) - Não conectando"
    fi
}

# Mostrar informações finais
show_final_info() {
    echo ""
    echo "🎉 CONFIGURAÇÃO CONCLUÍDA!"
    echo "========================="
    echo ""
    echo "📋 URLs de Acesso:"
    echo "   • Aplicação Laravel: http://localhost:8000"
    echo "   • Painel Filament:   http://localhost:8000/admin"
    echo "   • API Python:        http://localhost:8001"
    echo "   • Documentação API:  http://localhost:8001/docs"
    echo ""
    echo "🗄️ Credenciais MySQL:"
    echo "   • Host: localhost:3306"
    echo "   • Database: makis_ead_db"
    echo "   • User: makis_ead_user"
    echo "   • Password: admin@123456"
    echo ""
    echo "🔧 Comandos Úteis:"
    echo "   • Ver logs: docker-compose logs -f"
    echo "   • Parar serviços: docker-compose down"
    echo "   • Reiniciar: docker-compose restart"
    echo "   • Rebuild: docker-compose up -d --build"
    echo ""
    echo "⚠️  Próximos Passos:"
    echo "   1. Configure as variáveis de pagamento no .env"
    echo "   2. Acesse http://localhost:8000/admin para criar conteúdo"
    echo "   3. Teste a API em http://localhost:8001/docs"
    echo ""
}

# Menu de opções
show_menu() {
    echo ""
    echo "Escolha uma opção:"
    echo "1) Configuração completa (recomendado)"
    echo "2) Apenas iniciar serviços Docker"
    echo "3) Apenas instalar dependências"
    echo "4) Verificar serviços"
    echo "5) Sair"
    echo ""
    read -p "Opção: " choice
    
    case $choice in
        1)
            check_dependencies
            setup_environment
            install_php_dependencies
            install_node_dependencies
            start_docker_services
            setup_database
            verify_services
            show_final_info
            ;;
        2)
            start_docker_services
            verify_services
            ;;
        3)
            install_php_dependencies
            install_node_dependencies
            ;;
        4)
            verify_services
            ;;
        5)
            echo "Saindo..."
            exit 0
            ;;
        *)
            log_error "Opção inválida!"
            show_menu
            ;;
    esac
}

# Verificar se está no diretório correto
if [ ! -f "docker-compose.yml" ]; then
    log_error "Execute este script no diretório raiz do projeto Makis EAD"
    exit 1
fi

# Mostrar menu
show_menu
