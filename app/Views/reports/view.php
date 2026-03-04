<div class="space-y-6">
    <!-- Header com Ações -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200 pb-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-xs text-slate-400 uppercase tracking-widest">
                    <li><a href="/reports" class="hover:text-primary-600">Relatórios</a></li>
                    <li><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                    <li class="text-slate-600 font-bold"><?= $report['month_year'] ?></li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold text-slate-900">Relatório de Atividades</h2>
        </div>
        
        <div class="mt-4 md:mt-0 flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 shadow-sm transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir PDF
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-rose-50 border-l-4 border-rose-400 p-4 rounded-md">
            <p class="text-sm text-rose-700"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
        </div>
    <?php endif; ?>

    <!-- Banner de Feedback Existente -->
    <?php if ($report['manager_feedback']): ?>
        <div class="bg-primary-50 border-l-4 border-primary-500 p-6 rounded-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-bold text-primary-800 uppercase tracking-wider">Feedback do Gestor</h3>
                    <div class="mt-2 text-sm text-primary-700 italic">
                        "<?= nl2br(htmlspecialchars($report['manager_feedback'])) ?>"
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Esquerda: Conteúdo do Relatório -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Painel de Decisão do Gestor (Apenas para Manager/Admin e se status for submitted) -->
            <?php if ($user['role'] !== 'employee' && $report['status'] === 'submitted'): ?>
                <div class="bg-white rounded-xl border-2 border-primary-100 shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-primary-600 text-white">
                        <h3 class="text-sm font-bold uppercase tracking-widest">Avaliação do Gestor</h3>
                    </div>
                    <form action="/reports/review/<?= $report['id'] ?>" method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        
                        <div>
                            <label for="feedback" class="block text-xs font-bold text-slate-500 uppercase mb-2">Comentários / Feedback</label>
                            <textarea name="feedback" id="feedback" rows="3" class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm" placeholder="Opcional para aprovação, obrigatório para revisão..."></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 pt-2">
                            <button type="submit" name="action" value="approve" class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-md">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Aprovar Relatório
                            </button>
                            <button type="submit" name="action" value="reject" class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-white border-2 border-rose-200 text-rose-600 text-sm font-bold rounded-lg hover:bg-rose-50 transition-colors">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Solicitar Revisão
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Conteúdo Consolidado -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Conteúdo Gerado</h3>
                </div>
                <div class="p-6 prose prose-slate max-w-none">
                    <?php if ($report['content_draft']): ?>
                        <div class="text-slate-800 leading-relaxed">
                            <?= nl2br(htmlspecialchars($report['content_draft'])) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-slate-400 italic">Nenhum conteúdo gerado para este período.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Evidências -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Evidências e Anexos</h3>
                    <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded-full"><?= count($evidenceList) ?></span>
                </div>
                <div class="p-6">
                    <?php if (!empty($evidenceList)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($evidenceList as $ev): ?>
                                <div class="flex items-center p-3 border border-slate-100 rounded-lg hover:bg-slate-50 transition-colors">
                                    <div class="p-2 bg-primary-50 text-primary-600 rounded-md mr-3">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($ev['file_name']) ?></p>
                                        <p class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($ev['file_type']) ?></p>
                                    </div>
                                    <a href="https://eventossefa.com.br/gestor-ia/uploads/<?= htmlspecialchars($ev['file_path']) ?>" target="_blank" class="text-xs font-bold text-primary-600 hover:text-primary-800 uppercase tracking-tighter">Ver</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-slate-400 text-sm italic">Nenhuma evidência anexada a este relatório.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Metadados e Chat Contextual -->
        <div class="space-y-6">
            <!-- Info Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-50 pb-2">Informações Gerais</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase">Colaborador</dt>
                        <dd class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($report['user_name'] ?? $user['name']) ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase">Competência</dt>
                        <dd class="text-sm font-semibold text-slate-900"><?= $report['month_year'] ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase">Status do Workflow</dt>
                        <dd class="mt-1">
                            <?php
                                $statusColors = [
                                    'draft' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                                ];
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $statusColors[$report['status']] ?? 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                <?= strtoupper($report['status']) ?>
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 uppercase">Data de Envio</dt>
                        <dd class="text-sm text-slate-600"><?= $report['submission_date'] ? date('d/m/Y H:i', strtotime($report['submission_date'])) : 'Pendente' ?></dd>
                    </div>
                </dl>
            </div>

            <!-- Histórico do Chat (Resumo) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Memória do Chat</h3>
                </div>
                <div class="p-4 max-h-[400px] overflow-y-auto space-y-4">
                    <?php foreach ($messages as $msg): ?>
                        <div class="flex flex-col <?= $msg['sender'] === 'user' ? 'items-end' : 'items-start' ?>">
                            <span class="text-[10px] text-slate-400 uppercase font-bold mb-1"><?= $msg['sender'] === 'user' ? 'Colaborador' : 'Assistente IA' ?></span>
                            <div class="max-w-[90%] p-3 rounded-xl text-xs <?= $msg['sender'] === 'user' ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-slate-100 text-slate-700 rounded-tl-none' ?>">
                                <?= htmlspecialchars($msg['message']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
