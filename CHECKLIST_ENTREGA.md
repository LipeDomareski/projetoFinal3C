# Checklist de entrega — CarHub

Este arquivo separa o que já está implementado no código do que ainda precisa ser validado no computador de entrega.

## Funcionalidades obrigatórias

- [x] Registro com validação.
- [x] Login e sessão com token Sanctum.
- [x] Logout com revogação do token atual.
- [x] Rotas da aplicação protegidas no frontend e API protegida com `auth:sanctum`.
- [x] Perfil próprio com foto, username, nome, bio, contadores e posts.
- [x] Edição de nome, username, bio e foto de perfil.
- [x] Validação de username único na atualização.
- [x] Perfil de outro usuário com posts e contadores.
- [x] Seguir e deixar de seguir com atualização do contador.
- [x] Bloqueio de follow em si mesmo e par de follow único no banco.
- [x] Home com posts e sugestões de usuários ainda não seguidos.
- [x] Like/unlike com estado persistido e contagem.
- [x] Comentários na Home e no post individual.
- [x] Tela individual do post.
- [x] Exclusão do próprio post.
- [x] Busca por nome/username e listagem sem termo de busca.
- [x] Navegação Home, Search e Profile com item ativo.
- [x] Página 404.

## Backend / banco

- [x] Laravel REST API organizada com Models, Services e Controllers.
- [x] MySQL no `compose.yaml` do backend.
- [x] Migrations e relacionamentos.
- [x] Restrições únicas de username, like e follow.
- [x] Seeders com usuários, posts, comentários, likes e follows.
- [x] Respostas JSON e validações HTTP.
- [x] Swagger UI em `/docs` e OpenAPI em `/docs/openapi.yaml`.

## Docker

- [x] `frontend/Dockerfile`.
- [x] `frontend/compose.yaml`.
- [x] `backend/Dockerfile.dev`.
- [x] `backend/Dockerfile` de produção.
- [x] `backend/compose.yaml` contendo API + MySQL.
- [x] Volume persistente para MySQL.
- [x] Volume persistente para uploads.
- [x] `storage:link` recriado ao iniciar o backend de desenvolvimento.
- [x] Extensões PHP necessárias instaladas nas imagens.
- [x] Limites do PHP ajustados para aceitar os uploads de até 5 MB permitidos pela aplicação.

## Extras

- [x] Frontend em Vue.js.
- [x] API de stories com expiração real em 24 horas.
- [ ] Interface de stories no frontend.
- [ ] Destaques.
- [ ] Hospedagem pública.

## Validar antes da apresentação

- [ ] Copiar `.env.example` para `.env` no backend.
- [ ] Subir backend com `docker compose up -d --build`.
- [ ] Executar `docker compose exec backend php artisan migrate:fresh --seed` em banco de teste.
- [ ] Abrir `http://localhost:8000/docs` e testar o Swagger.
- [ ] Subir frontend com `docker compose up -d --build`.
- [ ] Testar registro e login em janela anônima.
- [ ] Criar um post novo com imagem e confirmar que `/storage/...` abre no navegador.
- [ ] Reiniciar os containers e confirmar que a imagem continua existindo.
- [ ] Testar follow/unfollow com dois usuários.
- [ ] Testar like, comentário e exclusão do próprio post.
- [ ] Confirmar que um usuário não consegue excluir post de outro usuário.
- [ ] Publicar o repositório no GitHub e confirmar que o `.env` não foi commitado.
