<div class="flex h-[calc(100vh-12rem)] flex-col md:flex-row gap-6">
    <!-- Coluna Esquerda: Editor Manual do Relatório -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Editor de Relatório</h2>
            </div>
            <div class="flex items-center gap-4">
                <span id="save-status" class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter transition-opacity">Salvo automaticamente</span>
                <button onclick="saveManual()" class="text-xs font-bold text-primary-600 hover:text-primary-700 uppercase">Salvar Agora</button>
            </div>
        </div>
        
        <div class="flex-1 p-0 relative">
            <textarea id="editor" 
                class="w-full h-full p-8 text-slate-800 leading-relaxed border-0 focus:ring-0 resize-none font-sans text-base placeholder:text-slate-300"
                placeholder="Comece a descrever suas atividades aqui..."><?= htmlspecialchars((string)($report['content_draft'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Evidence Quick View (Opcional no footer do editor) -->
        <div class="px-6 py-3 border-t border-slate-50 bg-slate-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Evidências: <span id="evidence-count"><?= count($evidenceList) ?></span></span>
                <button onclick="document.getElementById('upload-section').scrollIntoView({behavior:'smooth'})" class="text-[10px] font-bold text-slate-500 hover:text-slate-700 uppercase underline">Gerenciar Anexos</button>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Assistente IA Copiloto -->
    <div class="w-full md:w-96 flex flex-col bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
            <svg class="h-5 w-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"/></svg>
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Assistente Copiloto</h2>
        </div>

        <!-- Chat History -->
        <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/20">
            <?php if (empty($messages)): ?>
                <div class="text-center py-10 px-4">
                    <div class="h-12 w-12 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed italic">"Olá! Estou aqui para ajudar você a refinar seu relatório. Digite algo ou use os atalhos abaixo."</p>
                </div>
            <?php endif; ?>

            <?php foreach ($messages as $msg): ?>
                <div class="flex <?= $msg['sender'] === 'user' ? 'justify-end' : 'justify-start' ?>">
                    <div class="max-w-[90%] rounded-2xl px-4 py-2 text-xs shadow-sm 
                        <?= $msg['sender'] === 'user' ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-800 rounded-tl-none' ?>">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Quick Actions (Chips) -->
        <div class="px-4 py-2 flex flex-wrap gap-2 border-t border-slate-100 bg-white">
            <button onclick="askAI('Revise a gramática e clareza do meu texto atual.')" class="px-3 py-1 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 rounded-full text-[10px] font-bold border border-slate-200 transition-colors uppercase tracking-tighter">✨ Revisar Texto</button>
            <button onclick="askAI('O que falta no meu relatório para ele ficar mais profissional?')" class="px-3 py-1 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 rounded-full text-[10px] font-bold border border-slate-200 transition-colors uppercase tracking-tighter">❓ O que falta?</button>
            <button onclick="askAI('Baseado no meu histórico, tem algum projeto que esqueci de citar?')" class="px-3 py-1 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 rounded-full text-[10px] font-bold border border-slate-200 transition-colors uppercase tracking-tighter">🧠 Lembrar Projetos</button>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-slate-100 bg-white">
            <form id="chat-form" class="relative">
                <input type="text" id="message" required placeholder="Dúvida ou sugestão..." 
                    class="block w-full rounded-xl border-slate-200 py-3 pr-12 text-xs focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" id="send-button" class="absolute right-2 top-1.5 p-1.5 text-primary-600 hover:text-primary-700 transition-colors">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Upload Section (Scroll target) -->
<div id="upload-section" class="mt-8 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 max-w-4xl">
    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-widest mb-4">Evidências e Anexos</h3>
    <form id="upload-form" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-6 bg-slate-50 p-4 rounded-xl border border-dashed border-slate-300">
        <div class="md:col-span-1">
            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Arquivo (Max 10MB)</label>
            <input type="file" id="evidence" name="evidence" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
        </div>
        <div class="md:col-span-1">
            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Descrição</label>
            <input type="text" id="description" name="description" placeholder="Ex: Print do deploy..." class="block w-full rounded-lg border-slate-200 text-xs focus:ring-primary-500 focus:border-primary-500">
        </div>
        <button type="submit" class="bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-slate-50 shadow-sm transition-colors">Fazer Upload</button>
    </form>

    <div id="evidence-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($evidenceList as $ev): ?>
            <div class="flex items-center p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                <div class="p-2 bg-primary-100 text-primary-600 rounded-lg mr-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold text-slate-900 truncate"><?= htmlspecialchars($ev['file_name']) ?></p>
                    <p class="text-[9px] text-slate-400 uppercase"><?= htmlspecialchars($ev['description'] ?? 'Sem descrição') ?></p>
                </div>
                <a href="https://eventossefa.com.br/gestor-ia/uploads/<?= htmlspecialchars($ev['file_path']) ?>" target="_blank" class="ml-2 text-[9px] font-bold text-primary-600 hover:underline uppercase">Ver</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const editor = document.getElementById('editor');
    const messagesEl = document.getElementById('messages');
    const chatForm = document.getElementById('chat-form');
    const uploadForm = document.getElementById('upload-form');
    const saveStatus = document.getElementById('save-status');
    const csrfToken = "<?= $csrfToken ?>";

    // --- Lógica do Editor (Auto-save) ---
    let saveTimeout;
    editor.addEventListener('input', () => {
        saveStatus.textContent = "Digitando...";
        saveStatus.classList.remove('opacity-40');
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(saveManual, 3000); // Salva após 3 seg de inatividade
    });

    async function saveManual() {
        saveStatus.textContent = "Salvando...";
        try {
            const response = await fetch('/chat/save-draft', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    content: editor.value,
                    csrf_token: csrfToken
                })
            });
            if (response.ok) {
                saveStatus.textContent = "Alterações salvas";
                saveStatus.classList.add('opacity-40');
            }
        } catch (e) {
            saveStatus.textContent = "Erro ao salvar";
        }
    }

    // --- Lógica do Chat (Copiloto) ---
    function askAI(msg) {
        document.getElementById('message').value = msg;
        chatForm.dispatchEvent(new Event('submit'));
    }

    function appendSnippet(text) {
        const currentPos = editor.selectionStart;
        const value = editor.value;
        const newValue = value.substring(0, currentPos) + "\n" + text + "\n" + value.substring(editor.selectionEnd);
        editor.value = newValue;
        saveManual();
        editor.focus();
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('message');
        const message = input.value.trim();
        if (!message) return;

        appendMessage(message, 'user');
        input.value = '';
        
        const typingId = appendMessage('...', 'ai', true);

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    message: message,
                    editor_content: editor.value, // Envia o texto do editor como contexto
                    csrf_token: csrfToken
                })
            });

            document.getElementById(typingId).remove();
            const data = await response.json();

            if (response.ok) {
                appendMessage(data.assistant_message, 'ai', false, data.suggested_snippet);
            } else {
                appendMessage("Erro: " + (data.error || "Tente novamente"), 'ai');
            }
        } catch (e) {
            if (document.getElementById(typingId)) document.getElementById(typingId).remove();
            appendMessage("Erro de conexão com o servidor.", 'ai');
        }
    });

    function appendMessage(text, sender, isTyping = false, snippet = '') {
        const id = 'msg-' + Math.random().toString(36).substr(2, 9);
        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `max-w-[90%] rounded-2xl px-4 py-2 text-xs shadow-sm ${sender === 'user' ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-800 rounded-tl-none'}`;
        
        if (isTyping) bubble.classList.add('animate-pulse');
        bubble.innerHTML = text.replace(/\n/g, '<br>');

        if (snippet) {
            const btn = document.createElement('button');
            btn.className = "mt-3 block w-full py-2 bg-primary-50 text-primary-700 rounded-lg font-bold text-[10px] uppercase border border-primary-100 hover:bg-primary-100 transition-colors";
            btn.innerHTML = "➕ Copiar para o Editor";
            btn.onclick = () => appendSnippet(snippet);
            bubble.appendChild(btn);
        }

        wrapper.appendChild(bubble);
        messagesEl.appendChild(wrapper);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return id;
    }

    // --- Lógica de Upload ---
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fileInput = document.getElementById('evidence');
        if (!fileInput.files[0]) return;

        const formData = new FormData();
        formData.append('evidence', fileInput.files[0]);
        formData.append('description', document.getElementById('description').value);
        formData.append('csrf_token', csrfToken);

        try {
            const response = await fetch('/chat/upload', { method: 'POST', body: formData });
            const data = await response.json();
            if (response.ok) {
                location.reload(); // Simplificação para atualizar lista e contador
            } else {
                alert(data.error);
            }
        } catch (e) { alert("Erro no upload"); }
    });
</script>

<?php
$slot = ob_get_clean();
$pageTitle = 'Elaboração de Relatório';
require __DIR__ . '/../layouts/admin.php';
?>