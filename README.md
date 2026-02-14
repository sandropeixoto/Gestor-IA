# Gestor IA

Sistema inteligente de mentoria corporativa e automação de relatórios via Inteligência Artificial. O Gestor IA ajuda colaboradores a estruturarem suas atividades mensais e oferece aos gestores uma visão consolidada do desempenho da equipe.

## 🚀 Funcionalidades Principais

### 🧠 Inteligência Artificial (Mentor)
- **Chat Contextual**: Assistente que entende o contexto do usuário (Cargo, Área, Histórico).
- **Memória Persistente**: O sistema aprende fatos sobre o usuário e os utiliza em conversas futuras (`UserInsights`).
- **Rascunho Automático**: A IA gera proativamente o rascunho do relatório mensal baseado na conversa.

### 👥 Hierarquia e Gestão
- **Estrutura Organizacional**: Vínculo entre Colaborador e Gestor (`manager_id`).
- **Auto-serviço**: Usuários podem definir sua área de atuação e conectar-se ao seu gestor via email no Perfil.
- **Dashboard do Gestor**: Visão exclusiva para líderes acompanharem os relatórios de seus liderados diretos.

### 🔐 Segurança e Acesso
- **Autenticação Segura**: Login com proteção contra ataques de força bruta (simulado) e hash de senha.
- **Controle de Acesso (RBAC)**:
  - **Admin**: Acesso total ao sistema, logs e gestão de usuários.
  - **Manager**: Acesso aos relatórios do time.
  - **Employee**: Foco no próprio relatório e chat.
- **Proteção CSRF**: Tokens anti-falsificação em todos os formulários.

### 📊 Relatórios e Evidências
- **Ciclo Mensal**: Criação automática de relatórios baseados no mês corrente (`YYYY-MM`).
- **Upload de Evidências**: Anexo seguro de arquivos (PDF, Imagens) para compor o relatório.
- **Status Workflow**: Controle de estados (Rascunho -> Enviado).

## 🛠 Tech Stack
- **Backend**: PHP 8.x (MVC Customizado sem frameworks pesados).
- **Frontend**: HTML5, Vanilla JS e **TailwindCSS** (via CDN).
- **Banco de Dados**: MySQL 8.0.
- **Infraestrutura**: Docker-ready (opcional), servidor embutido PHP para dev.

## 🏁 Como Iniciar

### Pré-requisitos
- PHP 8.0 ou superior
- MySQL
- Composer (opcional)

### Passo a Passo
1. **Clone o repositório** e entre na pasta.
2. **Configure o ambiente**:
   ```bash
   cp .env.example .env
   # Edite o .env com suas credenciais de banco de dados
   ```
3. **Banco de Dados**:
   Execute os scripts SQL na ordem para criar a estrutura e dados iniciais:
   - `database/schema.sql` (Estrutura base)
   - `database/update_v2_work_area.sql` (Campos de perfil)
   - `database/update_v3_role_description.sql` (Descrição de cargo)
   - `database/update_v4_hierarchy.sql` (Hierarquia de gestores)
   - `database/seed.sql` (Usuários de teste)

4. **Inicie o Servidor**:
   ```bash
   php -S 0.0.0.0:8000 -t public
   ```
5. **Acesse**: [http://localhost:8000](http://localhost:8000)

## 👤 Usuários de Teste (Seed)

| Email | Senha | Perfil |
|-------|-------|--------|
| `admin@gestoria.local` | `Admin@123` | **Administrador** |
| `maria.gestora@gestoria.local` | `Manager@123` | **Gestor (Manager)** |
| `joao.colaborador@gestoria.local` | `Employee@123` | **Colaborador** |
| `ana.colaboradora@gestoria.local` | `Employee@123` | **Colaborador** |

## 📂 Estrutura do Projeto
- `app/`: Lógica da aplicação (Controllers, Models, Views, Core).
- `public/`: Entry point (`index.php`) e assets.
- `database/`: Scripts SQL de migração e seed.
- `config/`: Configurações globais (Banco, App via `.env`).

---
_Desenvolvido com foco em simplicidade, performance e UX moderna._
