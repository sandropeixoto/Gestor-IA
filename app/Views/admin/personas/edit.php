<div class="space-y-8">
    <div class="md:flex md:items-center md:justify-between">
        <h2 class="text-2xl font-bold text-slate-900">Editar Diretriz de IA</h2>
        <a href="<?= url('/admin/personas') ?>" class="text-sm text-primary-600 hover:text-primary-700 font-bold">Voltar para Lista</a>
    </div>

    <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm max-w-2xl">
        <form action="<?= url('/admin/personas/update/' . $persona['id']) ?>" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Área de Atuação</label>
                <input type="text" name="work_area" value="<?= htmlspecialchars($persona['work_area']) ?>" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Diretriz (Prompt)</label>
                <textarea name="prompt" rows="6" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 text-sm leading-relaxed"><?= htmlspecialchars($persona['prompt']) ?></textarea>
                <p class="mt-2 text-xs text-slate-400 italic">Descreva detalhadamente como a IA deve se comportar e quais pontos ela deve priorizar nesta área.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors shadow-md">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
