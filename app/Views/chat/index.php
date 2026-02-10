<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chat - <?= htmlspecialchars($appConfig['name'], ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f3f4f6; }
        .top { background: #111827; color: #fff; padding: .8rem 1rem; display: flex; justify-content: space-between; align-items: center; }
        .layout { display: grid; grid-template-columns: 1.1fr .9fr; gap: 1rem; padding: 1rem; height: calc(100vh - 64px); box-sizing: border-box; }
        .panel { background: #fff; border-radius: 10px; padding: 1rem; box-shadow: 0 4px 16px rgba(0,0,0,.06); overflow: auto; }
        .messages { height: 42%; overflow: auto; margin-bottom: 1rem; }
        .msg { margin: .5rem 0; padding: .6rem .8rem; border-radius: 8px; max-width: 90%; }
        .msg.user { background: #dbeafe; margin-left: auto; }
        .msg.ai { background: #e5e7eb; }
        .msg.typing { color: #9ca3af; }
        form { display: flex; gap: .5rem; }
        input[type=text] { flex: 1; padding: .65rem; border: 1px solid #d1d5db; border-radius: 8px; }
        button { padding: .65rem .9rem; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
        button:disabled { background: #9ca3af; cursor: not-allowed; }
        pre { white-space: pre-wrap; background: #f8fafc; padding: .75rem; border-radius: 8px; border: 1px solid #e5e7eb; }
        .upload-box { margin-top: 1rem; border-top: 1px solid #e5e7eb; padding-top: 1rem; }
        .upload-box input[type=file] { display:block; margin-bottom: .5rem; }
        .upload-box input[name=description] { width: 100%; margin-bottom: .5rem; }
        .evidence-list { list-style: none; padding-left: 0; }
        .evidence-list li { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: .55rem; margin-bottom: .5rem; }
        .hint { font-size: .85rem; color: #334155; }
    </style>
</head>
<body>
    <div class="top">
        <div>Chat de Relatório — Competência <?= htmlspecialchars($currentMonthYear, ENT_QUOTES, 'UTF-8') ?></div>
        <div>
            <a href="/dashboard" style="color:#fff; margin-right: 12px;">Dashboard</a>
            <form method="post" action="/logout" style="display:inline;"><button type="submit" style="background:#374151;">Sair</button></form>
        </div>
    </div>

    <div class="layout">
        <section class="panel">
            <h2>Entrevista com IA</h2>
            <div id="messages" class="messages">
                <?php foreach ($messages as $item): ?>
                    <div class="msg <?= $item['sender'] === 'user' ? 'user' : 'ai' ?>">
                        <?= nl2br(htmlspecialchars($item['message'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <form id="chat-form">
                <input id="message" type="text" name="message" placeholder="Descreva uma atividade realizada..." required />
                <button id="send-button" type="submit">Enviar</button>
            </form>

            <div class="upload-box">
                <h3>Evidências</h3>
                <form id="upload-form" enctype="multipart/form-data">
                    <input id="evidence" type="file" name="evidence" required />
                    <input id="description" type="text" name="description" placeholder="Descrição curta da evidência (opcional)" />
                    <button type="submit">Anexar arquivo</button>
                </form>
                <p class="hint">Tipos permitidos: pdf, xlsx, xls, doc, docx, jpg, jpeg, png. Limite: 10MB.</p>

                <ul id="evidence-list" class="evidence-list">
                    <?php foreach ($evidenceList as $evidence): ?>
                        <li>
                            <strong><?= htmlspecialchars((string) $evidence['file_name'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                            <small><?= htmlspecialchars((string) ($evidence['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="panel">
            <h2>Live Preview do Relatório</h2>
            <p><strong>Status:</strong> <span id="status"><?= htmlspecialchars((string) $report['status'], ENT_QUOTES, 'UTF-8') ?></span></p>
            <p><strong>Atualizado em:</strong> <span id="updated-at"><?= htmlspecialchars((string) ($report['updated_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span></p>
            <pre id="draft"><?= htmlspecialchars((string) ($report['content_draft'] ?? ''), ENT_QUOTES, 'UTF-8') ?></pre>
        </section>
    </div>

<script>
const form = document.getElementById('chat-form');
const uploadForm = document.getElementById('upload-form');
const messageInput = document.getElementById('message');
const sendButton = document.getElementById('send-button');
const messagesEl = document.getElementById('messages');
const draftEl = document.getElementById('draft');
const statusEl = document.getElementById('status');
const updatedAtEl = document.getElementById('updated-at');
const evidenceListEl = document.getElementById('evidence-list');
const evidenceInputEl = document.getElementById('evidence');
const descriptionInputEl = document.getElementById('description');

function setFormEnabled(enabled) {
    messageInput.disabled = !enabled;
    sendButton.disabled = !enabled;
}

function appendMessage(text, sender, isTyping = false) {
    const div = document.createElement('div');
    div.className = `msg ${sender}` + (isTyping ? ' typing' : '');
    div.innerHTML = text.replace(/\n/g, '<br>');
    messagesEl.appendChild(div);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return div;
}

function removeTypingIndicator() {
    const typingMsg = messagesEl.querySelector('.msg.typing');
    if (typingMsg) {
        typingMsg.remove();
    }
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const message = messageInput.value.trim();
    if (!message) return;

    appendMessage(message, 'user');
    messageInput.value = '';
    setFormEnabled(false);
    const typingIndicator = appendMessage('IA está processando...', 'ai', true);

    const body = new URLSearchParams({ message });

    const response = await fetch('/chat/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
        body
    });
    
    removeTypingIndicator();
    const payload = await response.json();

    if (!response.ok) {
        appendMessage(payload.error || 'Erro ao processar mensagem.', 'ai');
        setFormEnabled(true);
        return;
    }

    appendMessage(payload.assistant_message, 'ai');
    draftEl.textContent = payload.content_draft || '';
    statusEl.textContent = payload.status || 'draft';
    updatedAtEl.textContent = payload.updated_at || '-';
    setFormEnabled(true);
});

uploadForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (!evidenceInputEl.files.length) {
        appendMessage('Selecione um arquivo para anexar.', 'ai');
        return;
    }

    const formData = new FormData();
    formData.append('evidence', evidenceInputEl.files[0]);
    formData.append('description', descriptionInputEl.value || '');

    const response = await fetch('/chat/upload', {
        method: 'POST',
        body: formData
    });

    const payload = await response.json();

    if (!response.ok) {
        appendMessage(payload.error || 'Falha no upload da evidência.', 'ai');
        return;
    }

    appendMessage(`Evidência anexada: ${payload.file_name}.`, 'ai');

    const item = document.createElement('li');
    const description = payload.description ? payload.description : '';
    item.innerHTML = `<strong>${payload.file_name}</strong><br><small>${description}</small>`;
    evidenceListEl.prepend(item);

    uploadForm.reset();
});
</script>
</body>
</html>
