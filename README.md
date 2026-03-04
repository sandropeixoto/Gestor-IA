# 🤖 Gestor IA

O **Gestor IA** é uma plataforma corporativa inteligente de mentoria e automação de relatórios. O sistema utiliza Inteligência Artificial para ajudar colaboradores a estruturarem suas atividades mensais através de um chat contextual, gerando automaticamente rascunhos de relatórios que são enviados para revisão de seus gestores.

## 🛠 Stack Tecnológica

- **Backend:** PHP 8.1+ (Arquitetura MVC Customizada)
- **Frontend:** HTML5, Vanilla JavaScript, Tailwind CSS (via CDN)
- **Banco de Dados:** MySQL 8.0 / MariaDB
- **Integração IA:** OpenAI API / Gemini API (via LLMService)
- **Infraestrutura:** Docker, Cloud Build/Run (GCP)

## 🚀 Funcionalidades Principais

1. **Mentoria IA Contextual:** Chat que utiliza o histórico, cargo e área do usuário para guiar a criação do relatório.
2. **Aprendizado Contínuo (Insights):** A IA extrai aprendizados sobre o perfil do colaborador após cada submissão.
3. **Fluxo de Aprovação:** Hierarquia entre `Employee`, `Manager` e `Admin` para revisão de entregas.
4. **Gestão de Evidências:** Upload e vinculação de arquivos (PDF/Imagens) aos relatórios mensais.

## ⚙️ Instalação e Execução

### Pré-requisitos
- PHP 8.1 ou superior
- MySQL 8.0
- Docker (Opcional)

### Configuração do Ambiente
1. Clone o repositório.
2. Crie o arquivo `.env` baseado no `.env.example`:
   ```bash
   cp .env.example .env
   ```
3. Configure as credenciais do banco de dados e a chave da API de IA no `.env`.

### Banco de Dados
Execute os scripts na pasta `database/` na seguinte ordem:
1. `schema.sql` (Estrutura)
2. `update_*.sql` (Migrações pendentes)
3. `seed.sql` (Dados iniciais de teste)

### Servidor de Desenvolvimento
Para rodar sem Docker:
```bash
php -S 0.0.0.0:8000 -t public
```
Acesse em `http://localhost:8000`.

## 📂 Estrutura de Pastas
- `app/Core/`: Núcleo do framework (Roteamento, Auth, CSRF, Database).
- `app/Controllers/`: Manipuladores de requisições.
- `app/Models/`: Camada de dados e lógica de persistência.
- `app/Services/`: Integrações externas e lógica complexa (IA, Upload).
- `app/Views/`: Templates PHP e layouts.
- `public/`: Assets estáticos e ponto de entrada (`index.php`).
- `database/`: Scripts de migração e sementes de dados.
