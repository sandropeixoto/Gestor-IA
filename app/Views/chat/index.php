<div class="flex h-full flex-col md:flex-row bg-slate-50">
    <!-- Coluna Esquerda: Editor Manual do Relatório -->
    <div class="flex-1 flex flex-col bg-white border-r border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Editor de Relatório</h2>
            </div>
            <div class="flex items-center gap-4">
                <span id="save-status" class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter transition-opacity opacity-40">Salvo automaticamente</span>
                <button onclick="saveManual()" class="text-xs font-bold text-primary-600 hover:text-primary-700 uppercase">Salvar Agora</button>
            </div>
        </div>
        
        <div class="flex-1 relative overflow-hidden">
            <textarea id="editor" 
                class="w-full h-full p-8 md:p-12 text-slate-800 leading-relaxed border-0 focus:ring-0 resize-none font-sans text-lg placeholder:text-slate-200"
                placeholder="Comece a descrever suas atividades aqui..."><?= htmlspecialchars((string)($report['content_draft'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="px-6 py-3 border-t border-slate-50 bg-slate-50/30 shrink-0 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Evidências anexadas: <?= count($evidenceList) ?></span>
            <button onclick="document.getElementById('upload-panel').classList.toggle('hidden')" class="text-[10px] font-bold text-primary-600 hover:text-primary-700 uppercase underline">Gerenciar Anexos</button>
        </div>
    </div>

    <!-- Coluna Direita: Assistente IA Copiloto -->
    <div class="w-full md:w-[400px] flex flex-col bg-white overflow-hidden shadow-2xl z-10">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Assistente Copiloto</h2>
            </div>
        </div>

        <!-- Chat History -->
        <div id="messages" class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/20">
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
                    <div class="max-w-[90%] rounded-2xl px-4 py-3 text-xs shadow-sm 
                        <?= $msg['sender'] === 'user' ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-800 rounded-tl-none' ?>">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        
                        <?php if (!empty($msg['suggested_snippet'])): ?>
                            <button onclick="appendSnippet(<?= htmlspecialchars(json_encode($msg['suggested_snippet'])) ?>)" 
                                class="mt-3 block w-full py-2 bg-primary-50 text-primary-700 rounded-lg font-bold text-[10px] uppercase border border-primary-100 hover:bg-primary-100 transition-colors">
                                ➕ Copiar para o Editor
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Quick Actions (Chips) -->
        <div class="px-4 py-3 flex flex-wrap gap-2 border-t border-slate-100 bg-white shrink-0">
            <button onclick="askAI('Revise a gramática e clareza do meu texto atual.')" class="px-3 py-1.5 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 rounded-lg text-[10px] font-bold border border-slate-200 transition-colors uppercase tracking-tighter shadow-sm">✨ Revisar Texto</button>
            <button onclick="askAI('Quais atividades técnicas eu realizei e que ainda não citei?')" class="px-3 py-1.5 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 rounded-lg text-[10px] font-bold border border-slate-200 transition-colors uppercase tracking-tighter shadow-sm">❓ O que falta?</button>
            <button onclick="askAI('Crie um resumo executivo baseado no rascunho atual.')" class="px-3 py-1.5 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 rounded-lg text-[10px] font-bold border border-slate-200 transition-colors uppercase tracking-tighter shadow-sm">📝 Gerar Resumo</button>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-slate-100 bg-white shrink-0">
            <form id="chat-form" class="relative">
                <input type="text" id="message" required placeholder="Dúvida ou sugestão..." 
                    class="block w-full rounded-xl border-slate-200 py-4 pr-12 text-xs focus:ring-primary-500 focus:border-primary-500 shadow-inner bg-slate-50/30">
                <button type="submit" id="send-button" class="absolute right-2 top-2 p-2 text-primary-600 hover:text-primary-700 transition-colors">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Upload Panel (Hidden by default) -->
<div id="upload-panel" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in zoom-in duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Gerenciar Evidências</h3>
            <button onclick="document.getElementById('upload-panel').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-6">
            <form id="upload-form" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Arquivo</label>
                        <input type="file" id="evidence" name="evidence" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-bold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Descrição</label>
                        <input type="text" id="description" name="description" placeholder="Ex: Print do sistema..." class="block w-full rounded-lg border-slate-200 text-xs focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl text-xs font-bold hover:bg-primary-700 shadow-md transition-colors">Fazer Upload Remoto</button>
            </form>

            <div class="max-h-60 overflow-y-auto space-y-2">
                <?php foreach ($evidenceList as $ev): ?>
                    <div class="flex items-center justify-between p-3 border border-slate-100 rounded-xl bg-slate-50/50">
                        <div class="flex items-center min-w-0">
                            <div class="p-2 bg-white rounded-lg border border-slate-100 mr-3">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-slate-900 truncate"><?= htmlspecialchars($ev['file_name']) ?></p>
                                <p class="text-[9px] text-slate-400 uppercase"><?= htmlspecialchars($ev['description'] ?? 'Sem descrição') ?></p>
                            </div>
                        </div>
                        <a href="<?= url('/uploads/' . $ev['file_path']) ?>" target="_blank" class="text-[9px] font-bold text-primary-600 hover:underline uppercase">Ver</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const editor = document.getElementById('editor');
    const messagesEl = document.getElementById('messages');
    const chatForm = document.getElementById('chat-form');
    const uploadForm = document.getElementById('upload-form');
    const saveStatus = document.getElementById('save-status');
    const csrfToken = "<?= $csrfToken ?>";

    // --- Inicialização ---
    window.addEventListener('load', () => {
        messagesEl.scrollTop = messagesEl.scrollHeight;
        editor.focus();
    });

    // --- Lógica do Editor (Auto-save) ---
    let saveTimeout;
    editor.addEventListener('input', () => {
        saveStatus.textContent = "Digitando...";
        saveStatus.classList.remove('opacity-40');
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(saveManual, 3000);
    });

    async function saveManual() {
        saveStatus.textContent = "Salvando...";
        try {
            const response = await fetch('<?= url('/chat/save-draft') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ content: editor.value, csrf_token: csrfToken })
            });
            if (response.ok) {
                saveStatus.textContent = "Alterações salvas";
                saveStatus.classList.add('opacity-40');
            }
        } catch (e) { saveStatus.textContent = "Erro ao salvar"; }
    }

    // --- Lógica do Chat (Copiloto) ---
    function askAI(msg) {
        document.getElementById('message').value = msg;
        chatForm.dispatchEvent(new Event('submit'));
    }

    function appendSnippet(text) {
        const start = editor.selectionStart;
        const end = editor.selectionEnd;
        const value = editor.value;
        editor.value = value.substring(0, start) + "\n" + text + "\n" + value.substring(end);
        saveManual();
        editor.focus();
        editor.setSelectionRange(start + text.length + 2, start + text.length + 2);
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
            const response = await fetch('<?= url('/chat/send') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    message: message,
                    editor_content: editor.value,
                    csrf_token: csrfToken
                })
            });

            const text = await response.text();
            if (document.getElementById(typingId)) document.getElementById(typingId).remove();
            
            const data = JSON.parse(text);
            if (response.ok) {
                appendMessage(data.assistant_message, 'ai', false, data.suggested_snippet);
            } else {
                appendMessage("Erro: " + (data.error || "Tente novamente"), 'ai');
            }
        } catch (e) {
            if (document.getElementById(typingId)) document.getElementById(typingId).remove();
            appendMessage("Erro de conexão.", 'ai');
        }
    });

    function appendMessage(text, sender, isTyping = false, snippet = '') {
        const id = 'msg-' + Math.random().toString(36).substr(2, 9);
        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

        const bubble = document.createElement('div');
        bubble.className = `max-w-[90%] rounded-2xl px-4 py-3 text-xs shadow-sm ${sender === 'user' ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-800 rounded-tl-none'}`;
        
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
            const response = await fetch('<?= url('/chat/upload') ?>', { method: 'POST', body: formData });
            if (response.ok) {
                location.reload();
            } else {
                const data = await response.json();
                alert(data.error);
            }
        } catch (e) { alert("Erro no upload"); }
    });
</script>

<?php
$slot = ob_get_clean();
$pageTitle = 'Elaboração de Relatório';
require __DIR__ . '/../layouts/minimal.php';
?>