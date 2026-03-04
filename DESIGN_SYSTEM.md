# 🎨 Design System - Gestor IA

Diretrizes visuais e de UX para manter a consistência da interface.

## 🎨 Paleta de Cores

O sistema utiliza a escala do **Tailwind CSS** como base:

- **Primary (Brand):** `Indigo-600` (#4f46e5) - Usado em botões principais, links e estados ativos.
- **Background:** `Slate-50` (#f8fafc) - Fundo das páginas para reduzir fadiga visual.
- **Surface:** `White` (#ffffff) - Cards e containers.
- **Text Primary:** `Slate-900` (#0f172a) - Títulos e textos importantes.
- **Text Secondary:** `Slate-500` (#64748b) - Descrições e labels.
- **Success:** `Emerald-600` - Status 'Approved' ou 'Success'.
- **Warning:** `Amber-500` - Status 'Draft' ou alertas.
- **Error:** `Rose-600` - Erros críticos e ações de exclusão.

## 🔡 Tipografia

- **Fonte Principal:** `Inter` ou `sans-serif` nativo do sistema.
- **Escalabilidade:**
  - `h1`: 1.875rem (text-3xl), bold, Slate-900.
  - `h2`: 1.5rem (text-2xl), semibold, Slate-800.
  - `body`: 1rem (text-base), normal, Slate-600.

## 🧱 Componentes Padronizados

- **Cards:** Background branco, border-radius `rounded-xl`, shadow `shadow-sm`.
- **Inputs:** `border-slate-300`, focus em `ring-indigo-500`, padding `py-2 px-3`.
- **Botões (Primary):** Background `Indigo-600`, texto branco, transição suave no hover.
- **Status Badges:** Pílulas arredondadas com fundo claro e texto forte na cor do status.

## 📱 Diretrizes de UX

1. **Feedback de IA:** Durante a resposta da IA no chat, deve haver um indicador visual de "digitando" ou processamento.
2. **Empty States:** Listas de relatórios ou evidências vazias devem exibir ilustrações ou mensagens amigáveis.
3. **Mobile First:** A interface deve ser totalmente responsiva, priorizando o uso do chat em dispositivos móveis.
4. **Acessibilidade:** Contraste mínimo de 4.5:1 para textos e uso de tags semânticas (main, nav, section).
