<?php
$title = 'Chat - ' . ($appConfig['name'] ?? 'Gestor IA');
ob_start();
?>
<div class="flex h-screen flex-col bg-slate-50 overflow-hidden">
    <!-- Top Bar -->
    <header
        class="flex h-16 flex-shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6 lg:px-8 z-10">
        <div class="flex items-center gap-4">
            <h1 class="text-lg font-semibold text-slate-900">Chat &mdash; <span class="text-slate-500 font-normal">
                    <?= htmlspecialchars($currentMonthYear, ENT_QUOTES, 'UTF-8')?>
                </span></h1>
        </div>
        <div class="flex items-center gap-4">
            <a href="/dashboard" class="text-sm font-medium text-slate-600 hover:text-slate-900">Voltar ao Dashboard</a>
            <form method="post" action="/logout">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8')?>" />
                <button type="submit"
                    class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Sair</button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex min-h-0 flex-1">
        <!-- Chat Column -->
        <div class="flex w-96 flex-col border-r border-slate-200 bg-white">
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages">
                <!-- Welcome/Empty State -->
                <?php if (empty($messages)): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-slate-500">Comece a descrever suas atividades do mês.</p>
                </div>
                <?php
endif; ?>

                <?php foreach ($messages as $item): ?>
                <div class="flex <?= $item['sender'] === 'user' ? 'justify-end' : 'justify-start'?>">
                    <div class="relative max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm
                            <?= $item['sender'] === 'user'
        ? 'bg-primary-600 text-white rounded-tr-sm'
        : 'bg-slate-100 text-slate-900 rounded-tl-sm'?>">
                        <?= nl2br(htmlspecialchars($item['message'], ENT_QUOTES, 'UTF-8'))?>
                    </div>
                </div>
                <?php
endforeach; ?>
            </div>

            <!-- Input Area -->
            <div class="border-t border-slate-200 bg-slate-50 p-4">
                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="message" name="message" required placeholder="Digite sua mensagem..."
                        class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    <button type="submit" id="send-button"
                        class="rounded-md bg-primary-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Preview & Evidence Column -->
        <main class="flex flex-1 flex-col overflow-hidden bg-slate-50 min-w-0">
            <div class="flex-1 overflow-y-auto p-6 lg:p-10">
                <div class="mx-auto max-w-4xl space-y-6">
                    <!-- Status Header -->
                    <div
                        class="flex items-center justify-between rounded-lg bg-white p-4 shadow-sm border border-slate-200">
                        <div class="flex items-center gap-3">
                            <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                            <span class="text-sm font-medium text-slate-700">Live Preview</span>
                        </div>
                        <div class="text-xs text-slate-500">
                            Última atualização: <span id="updated-at" class="font-mono">
                                <?= htmlspecialchars((string)($report['updated_at'] ?? '-'), ENT_QUOTES, 'UTF-8')?>
                            </span>
                        </div>
                    </div>

                    <!-- Document Paper -->
                    <div class="bg-white p-8 lg:p-12 shadow-lg rounded-xl border border-slate-200 min-h-[500px]">
                        <div class="prose prose-slate max-w-none">
                            <pre id="draft"
                                class="whitespace-pre-wrap font-sans text-base leading-relaxed text-slate-800"><?= htmlspecialchars((string)($report['content_draft'] ?? ''), ENT_QUOTES, 'UTF-8')?></pre>
                        </div>
                    </div>

                    <!-- Evidence Section -->
                    <div class="rounded-lg bg-white p-6 shadow-sm border border-slate-200">
                        <h3 class="text-base font-semibold leading-6 text-slate-900 mb-4">Evidências e Anexos</h3>

                        <form id="upload-form"
                            class="mb-6 rounded-md bg-slate-50 p-4 border border-slate-200 border-dashed">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                                <div class="col-span-full">
                                    <label for="evidence"
                                        class="block text-sm font-medium leading-6 text-slate-900">Adicionar
                                        arquivo</label>
                                    <div class="mt-2 flex items-center gap-x-3">
                                        <input type="file" id="evidence" name="evidence"
                                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                </div>
                                <div class="col-span-full">
                                    <input type="text" id="description" name="description"
                                        placeholder="Descrição opcional"
                                        class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                </div>
                                <div class="col-span-full">
                                    <button type="submit"
                                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Upload</button>
                                </div>
                            </div>
                        </form>

                        <ul id="evidence-list" role="list"
                            class="divide-y divide-slate-100 rounded-md border border-slate-200">
                            <?php foreach ($evidenceList as $evidence): ?>
                            <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                                <div class="flex w-0 flex-1 items-center">
                                    <!-- Paperclip Icon -->
                                    <svg class="h-5 w-5 flex-shrink-0 text-slate-400" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.551a.75.75 0 111.061 1.06l-3.45 3.551a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-4 flex min-w-0 flex-1 gap-2">
                                        <span class="truncate font-medium">
                                            <?= htmlspecialchars((string)$evidence['file_name'], ENT_QUOTES, 'UTF-8')?>
                                        </span>
                                        <?php if (!empty($evidence['description'])): ?>
                                        <span class="flex-shrink-0 text-slate-400">|
                                            <?= htmlspecialchars((string)$evidence['description'], ENT_QUOTES, 'UTF-8')?>
                                        </span>
                                        <?php
    endif; ?>
                                    </div>
                                </div>
                            </li>
                            <?php
endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Scripts mantidos e adaptados para classes Tailwind
    const form = document.getElementById('chat-form');
    const uploadForm = document.getElementById('upload-form');
    const messageInput = document.getElementById('message');
    const sendButton = document.getElementById('send-button');
    const messagesEl = document.getElementById('messages');
    const draftEl = document.getElementById('draft');
    const updatedAtEl = document.getElementById('updated-at');
    const evidenceListEl = document.getElementById('evidence-list');
    const evidenceInputEl = document.getElementById('evidence');
    const descriptionInputEl = document.getElementById('description');

    // Get CSRF Token from layout or meta
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function setFormEnabled(enabled) {
        messageInput.disabled = !enabled;
        sendButton.disabled = !enabled;
        if (enabled) {
            messageInput.focus();
        }
    }

    function appendMessage(text, sender, isTyping = false) {
        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = `relative max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm ${sender === 'user' ? 'bg-primary-600 text-white rounded-tr-sm' : 'bg-slate-100 text-slate-900 rounded-tl-sm'}`;

        if (isTyping) {
            bubbleDiv.classList.add('animate-pulse');
            bubbleDiv.innerHTML = '...';
        } else {
            bubbleDiv.innerHTML = text.replace(/\n/g, '<br>');
        }

        wrapperDiv.appendChild(bubbleDiv);
        messagesEl.appendChild(wrapperDiv);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return wrapperDiv; // Retorna o wrapper para poder remover se for typing
    }

    function removeTypingIndicator() {
        // Remove last message if it was typing indicator (simplification)
        // Better way: track the element
        const lastChild = messagesEl.lastElementChild;
        const bubble = lastChild?.querySelector('.animate-pulse');
        if (bubble) {
            lastChild.remove();
        }
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        appendMessage(message, 'user');
        messageInput.value = '';
        setFormEnabled(false);

        appendMessage('...', 'ai', true); // Typing indicator

        const body = new URLSearchParams({ message, csrf_token: csrfToken });

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body
            });

            removeTypingIndicator(); // Ensure removal

            const payload = await response.json();

            if (!response.ok) {
                appendMessage(payload.error || 'Erro ao processar mensagem.', 'ai');
            } else {
                appendMessage(payload.assistant_message, 'ai');
                draftEl.textContent = payload.content_draft || '';
                updatedAtEl.textContent = payload.updated_at || '-';
            }
        } catch (e) {
            removeTypingIndicator();
            appendMessage('Erro de conexão.', 'ai');
        } finally {
            setFormEnabled(true);
        }
    });

    uploadForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!evidenceInputEl.files.length) {
            alert('Selecione um arquivo.');
            return;
        }

        const formData = new FormData();
        formData.append('evidence', evidenceInputEl.files[0]);
        formData.append('description', descriptionInputEl.value || '');
        formData.append('csrf_token', csrfToken);

        try {
            const response = await fetch('/chat/upload', {
                method: 'POST',
                body: formData
            });

            const payload = await response.json();

            if (!response.ok) {
                alert(payload.error || 'Erro no upload.');
            } else {
                const li = document.createElement('li');
                li.className = 'flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6';
                li.innerHTML = `
                <div class="flex w-0 flex-1 items-center">
                    <svg class="h-5 w-5 flex-shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M15.621 4.379a3 3 0 00-4.242 0l-7 7a3 3 0 004.241 4.243h.001l.497-.5a.75.75 0 011.064 1.057l-.498.501-.002.002a4.5 4.5 0 01-6.364-6.364l7-7a4.5 4.5 0 016.368 6.36l-3.455 3.553A2.625 2.625 0 119.52 9.52l3.45-3.551a.75.75 0 111.061 1.06l-3.45 3.551a1.125 1.125 0 001.587 1.595l3.454-3.553a3 3 0 000-4.242z" clip-rule="evenodd" /></svg>
                    <div class="ml-4 flex min-w-0 flex-1 gap-2">
                        <span class="truncate font-medium">${payload.file_name}</span>
                        ${payload.description ? `<span class="flex-shrink-0 text-slate-400">| ${payload.description}</span>` : ''}
                    </div>
                </div>
             `;
                evidenceListEl.prepend(li); // Add to top
                uploadForm.reset();
            }
        } catch (e) {
            alert('Erro ao enviar.');
        }
    });
</script>
<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>