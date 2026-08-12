# CarHub

CarHub é uma rede social inspirada no Instagram, focada em carros. O projeto usa:

- Laravel 13 + Sanctum no backend
- MySQL 8.4
- Vue 3 + Vite + Tailwind CSS no frontend
- Docker / Docker Compose
- Swagger UI para documentação da API

## 1. Subindo o backend

```bash
cd backend

cp .env.example .env
docker compose up -d --build
docker compose exec backend php artisan migrate --seed
```

Backend:

```text
http://localhost:8000
```

Swagger UI:

```text
http://localhost:8000/docs
```

O container de desenvolvimento recria automaticamente o `public/storage` ao iniciar.
Os uploads públicos ficam em um volume Docker persistente.

> Não use `docker compose down -v` se quiser manter banco e uploads.

## 2. Subindo o frontend

Em outro terminal:

```bash
cd frontend
docker compose up -d --build
```

Frontend:

```text
http://localhost:5173
```

A URL da API pode ser alterada com a variável `VITE_API_URL`.

Exemplo:

```bash
VITE_API_URL=http://localhost:8000/api docker compose up -d --build
```

## 3. Usuário de demonstração

Após executar os seeders:

```text
E-mail: demo@carhub.test
Senha: password
```

Os seeders também criam usuários, posts, comentários, likes e follows para facilitar os testes.

## 4. Principais funcionalidades

- Registro e login
- Logout com revogação do token Sanctum
- Perfil próprio
- Edição de nome, username, bio e foto
- Perfil de outros usuários
- Seguir / deixar de seguir
- Feed de posts
- Sugestões de usuários
- Busca por nome e username
- Upload de posts
- Likes
- Comentários
- Exclusão do próprio post
- Paginação
- Página 404
- Stories no backend com expiração em 24 horas

## 5. Estrutura Docker

### Frontend

```text
frontend/
├── Dockerfile
└── compose.yaml
```

### Backend

```text
backend/
├── Dockerfile.dev
├── Dockerfile
└── compose.yaml
```

O `Dockerfile.dev` usa `php artisan serve` para desenvolvimento.
O `Dockerfile` usa Apache e é destinado ao build de produção.

## 6. Storage

Para verificar os uploads:

```bash
cd backend
docker compose exec backend ls -lah storage/app/public
docker compose exec backend ls -lah storage/app/public/posts
```

Uma nova publicação deve gerar um arquivo em:

```text
storage/app/public/posts/
```

e ficar acessível por:

```text
http://localhost:8000/storage/posts/NOME_DO_ARQUIVO
```

## 7. Comandos úteis

Rotas:

```bash
docker compose exec backend php artisan route:list
```

Migrations:

```bash
docker compose exec backend php artisan migrate:status
```

Reexecutar seeders em um banco vazio:

```bash
docker compose exec backend php artisan db:seed
```

Reset completo do banco de desenvolvimento:

```bash
docker compose exec backend php artisan migrate:fresh --seed
```

**Atenção:** `migrate:fresh` apaga os dados existentes.
# projetoFinal3C
