# Gestor IA — Product Requirements Document (PRD)

- **Projeto:** Gestor IA: Sistema de Relatórios Automatizados via IA (MVP)
- **Versão:** 1.0
- **Status:** Planejamento Inicial

## 1. Visão geral e objetivo
Desenvolver uma aplicação web para automatizar a criação de relatórios mensais de atividades. O sistema utilizará um agente de IA para entrevistar colaboradores, extrair informações sobre tarefas e consolidar um relatório estruturado.

## 2. Público-alvo e personas
- **Colaborador (Usuário Final):** conversa com a IA, relata atividades e anexa evidências.
- **Gestor (Coordenador/Diretor):** visualiza relatórios consolidados e acompanha prazos.
- **Administrador:** gerencia usuários e hierarquia (no MVP pode ser o desenvolvedor).

## 3. Regras de negócio
- **Hierarquia flexível:** cada usuário pode apontar para um gestor via `manager_id`.
- **Fluxo de aprovação:** relatório fica em `draft` até o colaborador aprovar e enviar (`submitted`).
- **Ciclo mensal:** relatórios organizados por competência (`YYYY-MM`).
- **Intervenção da IA:** IA deve aprofundar respostas vagas mantendo foco em entrega.
- **Evidências:** anexos vinculados ao relatório; IA cita anexos sem processar conteúdo.

## 4. Requisitos funcionais

### 4.1 Módulo de chat e criação (Colaborador)
- Tela split-screen:
  - esquerda: chatbot interativo;
  - direita: live preview do relatório atualizado por AJAX/Fetch.
- Upload de evidências no chat.
- Botão de finalização: **Aprovar e Enviar para Chefia**.

### 4.2 Módulo de gestão (Dashboard Chefia)
- Lista de subordinados com status mensal (Pendente, Em Andamento, Entregue).
- Leitura do relatório consolidado e download de anexos.
- Exportação para PDF/Word.
- Alertas visuais de prazos próximos (configuráveis).

### 4.3 Backend (PHP + IA)
- Integração com APIs de LLM (OpenAI/Gemini/Anthropic) via cURL.
- Prompt de sistema: IA como entrevistadora corporativa focada em:
  - o que foi feito;
  - qual resultado;
  - dificuldades;
  - próximos passos.

## 5. Estrutura de dados (MySQL)
O script base está em [`database/schema.sql`](../database/schema.sql).

## 6. Arquitetura técnica sugerida (MVP)
- PHP 8.x
- MySQL 8.0 / MariaDB
- HTML5 + Bootstrap 5 + JavaScript para AJAX
- Bibliotecas sugeridas:
  - TCPDF ou PHPWord para exportação;
  - Dotenv para gerenciamento de segredos.

## 7. Fluxo funcional (resumo)
1. Usuário autentica; sistema cria/recupera relatório do mês.
2. Chat carrega histórico (`chat_logs`) e recebe nova mensagem.
3. Backend envia contexto + mensagem à IA.
4. IA responde com perguntas de aprofundamento e rascunho atualizado.
5. Backend salva rascunho em `reports.content_draft`.
6. Upload salva arquivo em `/uploads` + registro em `evidences`.
7. Colaborador finaliza envio mudando status para `submitted`.

## 8. Segurança (MVP)
- Prepared statements (anti SQL injection).
- Validação de extensões/mime em uploads.
- Controle de acesso por sessão para impedir leitura cruzada de relatórios.
