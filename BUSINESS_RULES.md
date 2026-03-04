# 💼 Regras de Negócio - Gestor IA

Este documento define os fluxos lógicos, restrições e comportamentos esperados do sistema.

## 👥 Perfis de Acesso (RBAC)

| Perfil | Descrição | Permissões |
| :--- | :--- | :--- |
| **Employee** | Colaborador padrão. | Interagir com a IA, gerenciar rascunhos, anexar evidências, enviar relatórios próprios. |
| **Manager** | Gestor de equipe. | Visualizar dashboards de liderados, aprovar/revisar relatórios de seu time (`manager_id`). |
| **Admin** | Administrador do sistema. | Gestão total de usuários, visualização de logs, configuração global do sistema. |

## 🔄 Fluxo do Relatório Mensal

1. **Geração Automática:** O sistema assegura a existência de um relatório (`draft`) para o usuário no mês/ano corrente (`YYYY-MM`) no primeiro acesso ao chat.
2. **Ciclo de Edição:** Enquanto o status for `draft`, o usuário pode enviar mensagens para a IA e anexar evidências.
3. **Bloqueio de Edição:** Uma vez que o relatório é submetido (`submitted`), o chat e o upload de evidências são bloqueados para aquele período.
4. **Aprovação:** O gestor pode alterar o status para `approved` após revisão.

## 🧠 Lógica de Mentoria IA

- **Contexto:** A IA deve receber obrigatoriamente a Área de Atuação (`work_area`) e a Descrição do Cargo (`role_description`) do usuário.
- **Insights:** Ao final de cada ciclo de submissão, a IA analisa o conteúdo e gera "Insights" (aprendizados) que são salvos no `UserInsightModel` para personalizar interações futuras.
- **Memória:** O chat deve enviar as últimas mensagens do histórico para manter a continuidade da conversa.

## 🛡️ Restrições e Validações

- **Relatório Único:** Um usuário só pode ter um único relatório por mês (`UNIQUE(user_id, month_year)`).
- **Hierarquia:** Um gestor só pode visualizar dados de usuários onde `users.manager_id = manager.id`.
- **Uploads:** Apenas formatos de imagem (JPG, PNG) e documentos (PDF) são permitidos. Tamanho máximo configurado via `UploadService`.
- **Segurança:** Todas as ações de escrita (POST) exigem validação de token CSRF.
