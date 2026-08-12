# Correções aplicadas

## Frontend

- Tela de registro implementada.
- Curtida do feed respeita `liked_by_me` e mantém a contagem de curtidas.
- Contagem de comentários exibida.
- URLs de mídia usam `VITE_API_URL` em vez de `localhost` hardcoded.
- Paginação do feed com botão "Carregar mais".
- Interceptor de 401 limpa a sessão.
- Route guard valida o token na API.
- Logout chama a API e revoga o token.
- Página 404 adicionada.
- Lógica repetida de likes/comentários centralizada em `usePostInteractions`.
- Perfil próprio e perfil de terceiros usam o mesmo componente.
- Follow/unfollow funcional com atualização do contador.
- Edição de perfil funcional, incluindo upload de foto.
- Exclusão do próprio post disponível na tela individual.
- Busca sem termo lista os usuários e busca por nome/username.
- Sugestões da Home agora excluem o próprio usuário e quem ele já segue.
- Navegação presente nas telas autenticadas e com indicação visual de rota ativa.
- Botão de comentário da tela individual foca o campo de comentário em vez de ser decorativo.
- Validação básica de tipo/tamanho de imagem no post e na foto de perfil.

## Backend

- Rotas duplicadas removidas e rotas privadas protegidas por Sanctum.
- Busca/perfil público movidos para `UserController` + `UserService`.
- Endpoint de sugestões separado da busca de usuários.
- Logout do Sanctum implementado.
- Posts/feed retornam `liked_by_me`, `liked_by_count` e `comments_count`.
- Exclusão de post também remove a mídia correspondente.
- Upload de foto de perfil implementado.
- Upload de stories alterado para arquivo real.
- Stories expirados não são retornados pela API.
- Seeder corrigido e ampliado; cada post de seed recebe uma mídia própria.
- Factory de usuário agora gera username único.
- Erro `dropCollum` corrigido para `dropColumn`.
- Campos privados do usuário não são serializados para outros usuários.
- Swagger UI e OpenAPI adicionados em `/docs`.

## Docker / storage

- Frontend possui exatamente `Dockerfile` e `compose.yaml` para Docker.
- Backend possui `Dockerfile.dev`, `Dockerfile` e `compose.yaml`.
- MySQL usa volume persistente e healthcheck.
- Uploads usam volume persistente.
- O symlink `public/storage` é recriado automaticamente no container de desenvolvimento.
- As imagens PHP agora instalam `pdo_mysql`, `mbstring`, `dom`, `xml`, `xmlwriter` e `zip`, necessárias pelo projeto/dependências.
- `upload_max_filesize` e `post_max_size` foram aumentados para comportar a validação de arquivos de até 5 MB.

## Observação sobre as imagens antigas

No projeto recebido, `storage/app/public` continha apenas o `.gitignore`; os arquivos antigos não vieram no ZIP. Registros antigos no banco podem, portanto, continuar apontando para arquivos inexistentes. A correção de volume/storage impede que **novos** uploads desapareçam em rebuilds, mas não consegue reconstruir arquivos antigos que já foram perdidos.
