# 🎓 Makis EAD - Plataforma de Ensino Online

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12.0">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Filament-3.0-FFAA00?style=for-the-badge" alt="Filament 3.0">
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/Status-Production%20Ready-success?style=for-the-badge" alt="Production Ready">
</p>

## 📋 Sobre o Projeto

**Makis EAD** (Étude Rapide) é uma plataforma completa de ensino à distância desenvolvida para a comunidade francófona, com foco especial no Haiti. A plataforma oferece:

- 🎯 Sistema completo de cursos online
- 🎮 Gamificação estilo Mimo (XP, streaks, badges)
- 💳 Múltiplos gateways de pagamento (Stripe, MercadoPago, MonCash)
- 👨‍🎓 Área do aluno com progresso tracking
- 🔐 Sistema de autenticação robusto
- 💰 Sistema de carteira digital
- 📊 Painel administrativo com Filament
- 🌍 Suporte multilíngue (Francês como padrão)

---

## 🏗️ Stack Tecnológica

### Backend
- **Laravel 12.0** - Framework PHP moderno
- **PHP 8.3** - Última versão estável
- **MySQL 8.0** - Banco de dados relacional
- **Redis 7** - Cache e sessions de alta performance
- **Laravel Sanctum** - Autenticação API

### Frontend
- **Vite** - Build tool moderna
- **Tailwind CSS** - Framework CSS utility-first
- **Alpine.js** - Framework JavaScript leve
- **Blade Templates** - Template engine do Laravel

### Admin Panel
- **Filament 3.0** - Painel administrativo completo
- CRUD automático para todas as entidades
- Dashboard com métricas em tempo real

### Gamificação
- **Python FastAPI** - API de gamificação
- Sistema de XP e níveis
- Streaks e recompensas
- Feedback inteligente

### Pagamentos
- **Stripe** (via Laravel Cashier)
- **MercadoPago** (América Latina)
- **PagSeguro** (Brasil)
- **MonCash** (Haiti)

### DevOps
- **Docker** - Containerização
- **Docker Compose** - Orquestração
- **Nginx** - Servidor web
- **Certbot** - SSL/HTTPS automático

---

## 🚀 Quick Start

### Pré-requisitos

- Docker 24.0+
- Docker Compose 2.0+
- Git

### Instalação (Desenvolvimento)

```bash
# 1. Clonar repositório
git clone <url-do-repositorio>
cd makis-ead

# 2. Copiar .env
cp .env.example .env

# 3. Configurar variáveis no .env
# Edite o arquivo .env com suas configurações

# 4. Subir containers
docker compose up -d

# 5. Instalar dependências
docker compose exec app composer install
docker compose exec app npm install

# 6. Gerar chave
docker compose exec app php artisan key:generate

# 7. Executar migrations
docker compose exec app php artisan migrate --seed

# 8. Criar usuário admin
docker compose exec app php artisan make:filament-user

# 9. Build assets
docker compose exec app npm run build
```

Acesse: http://localhost:8000

---

## 📦 Deploy em Produção

### Método Automatizado (Recomendado)

```bash
# 1. No servidor, clonar repositório
git clone <url-do-repositorio> /var/www/makis-ead
cd /var/www/makis-ead

# 2. Configurar .env
cp .env.example .env
nano .env  # Configure todas as variáveis

# 3. Executar script de deploy
chmod +x deploy.sh
./deploy.sh production
```

### Documentação Completa

Para instruções detalhadas de deploy, consulte:

- **[DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md)** - Checklist completo passo a passo
- **[ANALISE_FINAL.md](ANALISE_FINAL.md)** - Análise completa e arquitetura
- **[SETUP.md](SETUP.md)** - Setup para desenvolvimento local

---

## 📚 Estrutura do Projeto

```
makis-ead/
├── app/
│   ├── Filament/          # Recursos do painel admin
│   ├── Http/              # Controllers e Middleware
│   ├── Models/            # Modelos Eloquent
│   └── Services/          # Serviços (Pagamentos, etc)
├── database/
│   ├── migrations/        # 24 migrations (2014-2025)
│   └── seeders/           # Dados iniciais
├── docker/
│   ├── nginx/             # Configuração Nginx
│   └── mysql/             # Configuração MySQL
├── python_api/            # API de gamificação (FastAPI)
├── resources/
│   ├── views/             # Templates Blade
│   └── js/                # JavaScript/Alpine
├── routes/
│   ├── web.php            # Rotas web
│   └── auth.php           # Rotas de autenticação
├── Dockerfile.prod        # Dockerfile multi-stage otimizado
├── docker-compose.prod.yml # Compose para produção
├── deploy.sh              # Script de deploy automatizado
└── README.md              # Este arquivo
```

---

## 🎯 Funcionalidades Principais

### Para Alunos
- ✅ Navegação de cursos por categoria
- ✅ Sistema de carrinho de compras
- ✅ Múltiplas opções de pagamento
- ✅ Área do aluno com cursos matriculados
- ✅ Player de vídeo integrado
- ✅ Progresso de conclusão
- ✅ Sistema de XP e gamificação
- ✅ Certificados de conclusão

### Para Administradores
- ✅ Painel Filament completo
- ✅ Gestão de cursos, módulos e aulas
- ✅ Gestão de usuários e permissões
- ✅ Relatórios de vendas
- ✅ Gestão de pagamentos
- ✅ Dashboard com métricas
- ✅ Sistema de cupons/descontos

### Sistema de Pagamentos
- ✅ Stripe (cartão de crédito internacional)
- ✅ MercadoPago (América Latina)
- ✅ PagSeguro (Brasil)
- ✅ MonCash (Haiti - carteira digital)
- ✅ Sistema de assinaturas
- ✅ Carteira digital interna

---

## 🔧 Comandos Úteis

### Desenvolvimento

```bash
# Ver logs
docker compose logs -f

# Executar migrations
docker compose exec app php artisan migrate

# Limpar cache
docker compose exec app php artisan cache:clear

# Executar testes
docker compose exec app php artisan test

# Acessar container
docker compose exec app bash
```

### Produção

```bash
# Ver status
docker compose -f docker-compose.prod.yml ps

# Ver logs
docker compose -f docker-compose.prod.yml logs -f

# Reiniciar serviços
docker compose -f docker-compose.prod.yml restart

# Backup do banco
docker exec makis_ead_db_prod mysqldump -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE > backup.sql
```

---

## 📊 Arquitetura

```
┌─────────────────────────────────────────────────────────────┐
│                     NGINX (Port 80/443)                     │
│                    SSL/HTTPS com Certbot                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
       ┌───────────────┴───────────────┐
       │                               │
┌──────▼──────┐              ┌─────────▼────────┐
│  Laravel    │              │   Python API     │
│  PHP-FPM    │◄────────────►│   FastAPI        │
│  (Port 9000)│              │   (Port 8000)    │
└──────┬──────┘              └─────────┬────────┘
       │                               │
       │         ┌─────────────────────┤
       │         │                     │
┌──────▼─────────▼──────┐    ┌────────▼────────┐
│      MySQL 8.0        │    │   Redis 7       │
│    (Port 3306)        │    │  (Port 6379)    │
└───────────────────────┘    └─────────────────┘
```

---

## 🔒 Segurança

- ✅ SSL/HTTPS obrigatório em produção
- ✅ Firewall configurado (UFW)
- ✅ Fail2Ban para proteção contra ataques
- ✅ MySQL não exposto publicamente
- ✅ Senhas hasheadas (bcrypt)
- ✅ CSRF protection
- ✅ XSS protection
- ✅ SQL injection protection (Eloquent ORM)

---

## 📈 Performance

- ✅ OPcache habilitado
- ✅ Redis para cache e sessions
- ✅ Query optimization
- ✅ Assets minificados e comprimidos
- ✅ Lazy loading de imagens
- ✅ CDN ready

---

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

---

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 📞 Suporte

- 📧 Email: suporte@etuderapide.com
- 📚 Documentação: [DEPLOY_CHECKLIST.md](DEPLOY_CHECKLIST.md)
- 🐛 Issues: [GitHub Issues](https://github.com/seu-usuario/makis-ead/issues)

---

## 🙏 Agradecimentos

- Laravel Framework
- Filament Admin Panel
- Comunidade Open Source

---

**Desenvolvido com ❤️ para a comunidade francófona**

