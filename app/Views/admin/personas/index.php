<div class="space-y-8">
    <div class="md:flex md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-slate-900">Diretrizes de IA (Personas)</h2>
        <p class="text-sm text-slate-500">Gerencie como a IA se comporta em cada área de atuação.</p>
    </div>

    <!-- Formulário de Nova Persona -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Adicionar Nova Diretriz</h3>
        <form action="<?= url('/admin/personas/store') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Área de Atuação</label>
                <input type="text" name="work_area" required placeholder="Ex: Financeiro, TI..." class="block w-full rounded-lg border-slate-300 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Prompt / Persona</label>
                <input type="text" name="prompt" required placeholder="Você é um assistente..." class="block w-full rounded-lg border-slate-300 text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="inline-flex justify-center items-center px-4 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors">
                Salvar Persona
            </button>
        </form>
    </div>

    <!-- Tabela de Personas -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Área</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Diretriz (Prompt)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php foreach ($personas as $p): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900"><?= htmlspecialchars($p['work_area']) ?></td>
                    <td class="px-6 py-4 text-sm text-slate-600 italic">
                        <div class="max-w-md truncate" title="<?= htmlspecialchars($p['prompt']) ?>">
                            <?= htmlspecialchars($p['prompt']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-3">
                            <a href="<?= url('/admin/personas/edit/' . $p['id']) ?>" class="text-primary-600 hover:text-primary-900">Editar</a>
                            <form action="<?= url('/admin/personas/delete/' . $p['id']) ?>" method="POST" onsubmit="return confirm('Excluir esta diretriz?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="text-rose-600 hover:text-rose-900">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
