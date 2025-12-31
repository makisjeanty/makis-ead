# 🧪 Guia de Rotas para Teste

Este documento lista as principais rotas disponíveis no sistema para teste, tanto do Backend Laravel quanto da API Python (Gamification).

## 🟢 1. Rotas Públicas (Laravel)
Estas rotas podem ser acessadas diretamente no navegador sem login.

| Método | Rota | Descrição |
| :--- | :--- | :--- |
| `GET` | `/` | Página inicial (Landing Page) |
| `GET` | `/cursos` | Catálogo de cursos |
| `GET` | `/cursos/{slug}` | Detalhes de um curso (ex: `/cursos/python-basico`) |
| `GET` | `/login` | Página de login |
| `GET` | `/register` | Página de registro |
| `GET` | `/checkout` | Página de checkout (vazia se carrinho vazio) |

## 🟠 2. Rotas do Aluno (Requer Login)
Para testar, faça login com um usuário comum (ex: `aluno@makis.com`).

| Método | Rota | Descrição |
| :--- | :--- | :--- |
| `GET` | `/dashboard` | Painel do aluno |
| `GET` | `/aluno/meus-cursos` | Lista de cursos comprados |
| `GET` | `/aluno/curso/{slug}/aula` | Sala de aula (player de vídeo) |
| `GET` | `/perfil` | Configurações do perfil |

## 🔴 3. Rotas Administrativas (Requer Admin)
Acesse `/admin/login` com credenciais de administrador.

| Método | Rota | Descrição |
| :--- | :--- | :--- |
| `GET` | `/admin` | Dashboard administrativo (Filament) |
| `GET` | `/admin/courses` | Gerenciamento de cursos |
| `GET` | `/admin/users` | Gerenciamento de usuários |
| `GET` | `/admin/categories` | Gerenciamento de categorias |

## 🐍 4. API Python (Gamification)
Estas rotas são consumidas pelo frontend via JavaScript, mas podem ser testadas via Postman/Curl.
**Base URL:** `https://etuderapide.com/api/python` (ou `http://localhost:8001` localmente)

| Método | Rota | Auth? | Descrição | Payload Exemplo |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/` | Não | Verifica status da API | N/A |
| `GET` | `/dashboard/stats` | Sim | Estatísticas de engajamento | N/A |
| `POST` | `/learn/check-answer` | Sim | Valida exercício de código | `{"lesson_id": 1, "user_answer": "print('Ola Mundo')"}` |

### 🛠️ Como testar a API Python manualmente
Você precisa de um **Token Bearer** (gerado pelo Laravel Sanctum ao logar).

**Exemplo de teste (Curl):**
```bash
# 1. Obter Token (Logue no Laravel e pegue o cookie ou token da sessão)
# Ou use um token de API criado no perfil do usuário

TOKEN="seu_token_aqui"

# 2. Testar Status
curl https://etuderapide.com/api/python/

# 3. Testar Estatísticas
curl -H "Authorization: Bearer $TOKEN" https://etuderapide.com/api/python/dashboard/stats

# 4. Testar Validação
curl -X POST https://etuderapide.com/api/python/learn/check-answer \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"lesson_id": 1, "user_answer": "print(\"Ola Mundo\")"}'
```
