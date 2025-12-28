SETUP - Ambiente local

Passos mínimos para preparar o projeto localmente (Windows / PowerShell). Ajuste para Linux/macOS conforme necessário.

Pré-requisitos
- PHP >= 8.2 (CLI)
- Composer
- Node.js + npm
- (opcional) Docker / MySQL para ambiente completo

Passos (PowerShell)

1. Copiar variáveis de ambiente

```powershell
Push-Location 'F:\workspace\backend\php\makis-ead'
if (!(Test-Path .env)) { Copy-Item .env.example .env }
```

2. Instalar dependências PHP

```powershell
composer install --no-interaction --prefer-dist
```

Nota: se o Composer reclamar de ext-intl (intl), habilite a extensão no seu PHP (ver seção abaixo). Como alternativa temporária você pode usar: composer install --ignore-platform-req=ext-intl — porém não é recomendado em produção.

3. Instalar dependências JS

```powershell
npm ci || npm install
```

4. Gerar chave de aplicativo

```powershell
php artisan key:generate
```

5. Linkar storage (se necessário)

```powershell
php artisan storage:link
```

6. Rodar migrations (opcional para ambiente com DB)

```powershell
php artisan migrate --seed
```

Rodar testes (modo rápido usando sqlite em memória)

Para executar a suíte de testes sem precisar configurar banco MySQL local, use sqlite em memória:

```powershell
$env:DB_CONNECTION='sqlite'
$env:DB_DATABASE=':memory:'
php artisan test --no-coverage
```

Isso executa os testes usando a configuração em tempo de execução; os testes da cópia local foram validados com esse método.

Habilitar ext-intl

Ubuntu/Debian (exemplo para PHP 8.2):

```bash
sudo apt-get update
sudo apt-get install -y php8.2-intl
sudo systemctl restart php8.2-fpm  # ou reinicie o serviço PHP/Apache conforme seu setup
```

Windows (XAMPP / PHP standalone):
- Abra php.ini usado pelo seu CLI (ver php --ini) e remova o ; da linha extension=php_intl.dll, ou adicione se ausente.
- Reinicie o serviço web/terminal.

Após habilitar intl, reexecute composer install sem --ignore-platform-req.

Observações rápidas
- Não comite credenciais sensíveis em .env.
- Se preferir usar Docker, veja docker-compose.yml para orquestração dos serviços (app, nginx, python_api, db).
- Se quiser que eu atualize README.md com um resumo destes passos, ou faça commit/push automático, me diga.

Produção com Docker
-------------------

Para deploy em VPS usando Docker, há um arquivo `docker-compose.prod.yml` que cria imagens construídas (sem bind-mounts). Resumo:

- Preparar `.env` local com as variáveis `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`.
- Build e deploy:

```bash
docker compose -f docker-compose.prod.yml up --build -d
```

- Recomenda-se ajustar `Dockerfile` para executar `composer install --no-dev --optimize-autoloader` durante a build (multi-stage) e copiar apenas `vendor` + `public/build` para a imagem final.
- Não publique a porta do MySQL em produção; o `docker-compose.prod.yml` não publica `3306`.

Se quiser, posso:
- Criar um `Dockerfile` multi-stage que rode `composer install` no build e gere uma imagem otimizada.
- Automatizar um script de deploy (build -> push para registry -> `docker pull` + `docker compose up -d`) para a VPS.
