# Plano de Implementação — Gestor IA (MVP)

Este documento transforma o PRD em passos executáveis para iniciar e entregar o MVP com PHP + MySQL.

## 1) Preparação do projeto (Semana 1)

### Objetivo
Subir a base técnica com ambiente local, estrutura de pastas e banco funcional.

### Tarefas
1. Definir padrão do projeto PHP (sugestão: estrutura MVC simples).
2. Criar bootstrap inicial:
   - `public/index.php`
   - `app/Controllers`, `app/Services`, `app/Models`, `app/Views`
   - `config/` para DB, sessão e variáveis de ambiente.
3. Configurar `.env` com:
   - conexão MySQL;
   - chave da API LLM;
   - diretório de uploads.
4. Executar `database/schema.sql` em ambiente local.
5. Criar seed mínimo de usuários:
   - 1 admin, 1 gestor, 2 colaboradores com `manager_id`.

### Critério de aceite
- Aplicação abre em navegador sem erro.
- Conexão com banco funcionando.
- Tabelas e dados seed criados.

---

## 2) Autenticação e autorização (Semana 1-2)

### Objetivo
Garantir acesso seguro por perfil (admin, manager, employee).

### Tarefas
1. Implementar login/logout com sessão PHP.
2. Criar middleware/check de autenticação por rota.
3. Implementar controle por papel:
   - colaborador: apenas seu relatório/chat;
   - gestor: equipe subordinada direta;
   - admin: gestão completa (MVP básico).
4. Proteger contra acesso indevido via manipulação de IDs na URL.

### Critério de aceite
- Usuário não autenticado não acessa páginas internas.
- Colaborador não visualiza relatório de outros usuários.
- Gestor visualiza apenas subordinados.

---

## 3) Ciclo de relatório mensal (Semana 2)

### Objetivo
Criar/reutilizar relatório do mês automaticamente.

### Tarefas
1. Na entrada do colaborador, buscar relatório por `user_id` + `month_year`.
2. Se não existir, criar em `draft`.
3. Exibir status atual (`draft` / `submitted` / `approved`).
4. Persistir `updated_at` em cada atualização de conteúdo.

### Critério de aceite
- Um único relatório por colaborador por mês.
- Status e conteúdo atualizam corretamente.

---

## 4) Chat com IA + Live Preview (Semana 2-3)

### Objetivo
Entrevista automatizada com atualização do rascunho em tempo real.

### Tarefas
1. Criar UI split-screen:
   - esquerda: histórico + input de chat;
   - direita: prévia do relatório.
2. Endpoint para envio de mensagem (`POST /chat/send`).
3. Salvar mensagens em `chat_logs` (usuário e IA).
4. Implementar serviço `LLMService` (cURL) com system prompt de entrevistadora corporativa.
5. Atualizar `reports.content_draft` com o texto consolidado retornado.
6. Retornar JSON para atualizar preview via Fetch/AJAX.

### Critério de aceite
- Mensagens persistem no banco.
- Resposta da IA retorna e aparece na tela.
- Preview lateral atualiza sem recarregar a página.

---

## 5) Upload de evidências (Semana 3)

### Objetivo
Permitir anexos com segurança e vínculo ao relatório.

### Tarefas
1. Criar endpoint de upload com validação de extensão/MIME.
2. Limitar tamanho por arquivo (ex.: 10MB no MVP).
3. Salvar arquivo em `/uploads/{report_id}/`.
4. Registrar metadados em `evidences`.
5. Exibir lista de anexos no chat e no preview.

### Critério de aceite
- Upload válido é salvo e listado.
- Upload inválido (tipo/tamanho) é bloqueado com mensagem clara.

---

## 6) Finalização e envio para chefia (Semana 3-4)

### Objetivo
Concluir fluxo de aprovação do colaborador.

### Tarefas
1. Criar botão “Aprovar e Enviar para Chefia”.
2. Alterar status para `submitted` e preencher `submission_date`.
3. Bloquear edição após envio (MVP) ou permitir apenas com reabertura manual pelo gestor.

### Critério de aceite
- Relatório enviado aparece como entregue no dashboard do gestor.
- Colaborador não consegue editar rascunho após envio (regra MVP).

---

## 7) Dashboard da chefia (Semana 4)

### Objetivo
Dar visibilidade da equipe e entregas do mês.

### Tarefas
1. Listar subordinados diretos por `manager_id`.
2. Mostrar status mensal por colaborador:
   - Pendente (sem relatório),
   - Em andamento (`draft`),
   - Entregue (`submitted`/`approved`).
3. Tela de leitura do relatório + anexos.
4. Implementar filtro por competência (`month_year`).

### Critério de aceite
- Gestor acompanha equipe por mês.
- Consegue abrir relatório e baixar evidências.

---

## 8) Exportação (PDF/Word) (Semana 4-5)

### Objetivo
Permitir gerar documento final para distribuição.

### Tarefas
1. Escolher biblioteca (TCPDF ou PHPWord).
2. Criar endpoint de exportação por relatório.
3. Incluir no documento:
   - dados do colaborador;
   - competência;
   - conteúdo consolidado;
   - lista de evidências.

### Critério de aceite
- Download de arquivo válido e legível.

---

## 9) Notificações de prazo (MVP simplificado) (Semana 5)

### Objetivo
Alertar gestor sobre prazos próximos/atrasados.

### Tarefas
1. CRUD simples de `deadlines` por gestor e mês.
2. Regra visual no dashboard:
   - verde: no prazo;
   - amarelo: prazo próximo;
   - vermelho: atrasado.

### Critério de aceite
- Dashboard exibe alertas conforme data atual vs `deadline_date`.

---

## 10) Endurecimento e go-live interno (Semana 5-6)

### Objetivo
Preparar MVP para uso real interno com risco controlado.

### Tarefas
1. Revisão de segurança:
   - prepared statements;
   - validação de inputs;
   - proteção de sessão.
2. Logs básicos de erro e auditoria mínima (login/envio relatório/upload).
3. Backup diário do banco e estratégia de retenção.
4. Teste com grupo piloto (1 gestor + 3 colaboradores).
5. Coletar feedback e priorizar backlog pós-MVP.

### Critério de aceite
- Fluxo principal completo funciona de ponta a ponta em ambiente interno.
- Feedback do piloto registrado com plano de melhorias.

---

## Backlog pós-MVP (prioridade alta)
- Aprovação formal do gestor (`approved`) com comentários.
- Reabertura de relatório enviado.
- Suporte a múltiplos níveis hierárquicos na visão agregada.
- Notificações por e-mail/Teams.
- Métricas de produtividade por equipe e período.
- Versionamento de rascunho e trilha de auditoria.

## Sequência recomendada de execução
1. Infra + Auth
2. Relatório mensal
3. Chat IA + Preview
4. Upload
5. Envio
6. Dashboard gestor
7. Exportação
8. Prazos
9. Hardening + piloto


## Progresso de execução
- [x] Etapa 1: Preparação do projeto
- [x] Etapa 2: Autenticação e autorização
- [x] Etapa 3: Ciclo de relatório mensal (criação/recuperação automática)
- [x] Etapa 4: Chat com IA + Live Preview
- [x] Etapa 5: Upload de evidências
- [ ] Etapa 6 em diante
