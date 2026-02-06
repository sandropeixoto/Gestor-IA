# Gestor IA (MVP)

Repositório inicial com documentação de requisitos e esquema de banco para o MVP do sistema de relatórios automatizados via IA.

## Conteúdo
- `docs/PRD.md`: documento de requisitos do produto.
- `docs/IMPLEMENTATION_PLAN.md`: plano por fases para implementação efetiva do MVP.
- `database/schema.sql`: estrutura relacional inicial em MySQL.
- `database/seed.sql`: dados iniciais para ambiente local.

## Como iniciar (bootstrap atual)
1. Copie o arquivo de ambiente:
   - `cp .env.example .env`
2. Execute o schema e seed no MySQL:
   - `database/schema.sql`
   - `database/seed.sql`
3. Suba o servidor local PHP:
   - `php -S 0.0.0.0:8000 -t public`
4. Acesse no navegador:
   - `http://localhost:8000`

## Próximos passos sugeridos
1. Implementar autenticação e autorização por perfil.
2. Implementar fluxo de chat + geração de rascunho.
3. Implementar upload seguro de evidências.
4. Criar dashboard de gestão por hierarquia (`manager_id`).
5. Implementar exportação em PDF/Word.


## Credenciais seed (desenvolvimento)
- admin@gestoria.local / `Admin@123`
- maria.gestora@gestoria.local / `Manager@123`
- joao.colaborador@gestoria.local / `Employee@123`
- ana.colaboradora@gestoria.local / `Employee@123`


## Progresso de implementação
- ✅ Etapa 1: bootstrap inicial
- ✅ Etapa 2: autenticação e autorização base
- ✅ Etapa 3: ciclo mensal com criação/recuperação automática do relatório por competência (`YYYY-MM`)
- ✅ Etapa 4: chat com IA (MVP fallback) + live preview via AJAX
- ✅ Etapa 5: upload seguro de evidências com registro em banco e listagem no chat
