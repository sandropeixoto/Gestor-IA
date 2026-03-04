<div class="space-y-6">
    <!-- Header Contextual -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Central de Relatórios</h2>
            <p class="mt-1 text-sm text-slate-500 italic">Visualize e filtre todo o histórico de atividades geradas pelo sistema.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="/chat" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Novo Relatório
            </a>
        </div>
    </div>

    <!-- Barra de Filtros (Simulada para visualização) -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Status</label>
                <select name="status" class="block w-full rounded-md border-slate-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Todos os Status</option>
                    <option value="draft" <?= ($status === 'draft') ? 'selected' : '' ?>>Rascunho</option>
                    <option value="submitted" <?= ($status === 'submitted') ? 'selected' : '' ?>>Enviado</option>
                    <option value="approved" <?= ($status === 'approved') ? 'selected' : '' ?>>Aprovado</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Período</label>
                <input type="month" name="period" value="<?= htmlspecialchars($monthYear ?? '') ?>" class="block w-full rounded-md border-slate-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-100 text-slate-700 rounded-md text-sm font-semibold hover:bg-slate-200 transition-colors">
                    Aplicar Filtros
                </button>
                <a href="/reports" class="ml-2 text-sm text-slate-400 hover:text-slate-600 underline">Limpar</a>
            </div>
        </form>
    </div>

    <!-- Tabela Principal -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Mês/Ano</th>
                    <?php if ($user['role'] !== 'employee'): ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Colaborador</th>
                    <?php endif; ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Última Atualização</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach ($allReports as $rep): ?>
                <?php
                    $statusColors = [
                        'draft' => 'bg-amber-100 text-amber-700',
                        'submitted' => 'bg-blue-100 text-blue-700',
                        'approved' => 'bg-emerald-100 text-emerald-700',
                    ];
                    $statusLabels = [
                        'draft' => 'Rascunho',
                        'submitted' => 'Enviado',
                        'approved' => 'Aprovado',
                    ];
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                        <?= $rep['month_year'] ?>
                    </td>
                    <?php if ($user['role'] !== 'employee'): ?>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?= htmlspecialchars($rep['user_name']) ?>
                    </td>
                    <?php endif; ?>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $statusColors[$rep['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                            <?= $statusLabels[$rep['status']] ?? $rep['status'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 italic">
                        <?= date('d/m/Y H:i', strtotime($rep['updated_at'])) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="/reports/view/<?= $rep['id'] ?>" class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg title="Visualizar">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($allReports)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="text-slate-400">
                            <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-lg font-medium">Nenhum relatório encontrado</p>
                            <p class="text-sm">Tente ajustar seus filtros ou comece um novo relatório no chat.</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
