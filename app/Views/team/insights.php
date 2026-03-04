<div class="space-y-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Dashboard de Inteligência</h2>
            <p class="mt-1 text-sm text-slate-500">Mapeamento de competências e comportamentos extraídos pela IA através dos relatórios.</p>
        </div>
    </div>

    <!-- Indicadores de Resumo -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total de Aprendizados</dt>
            <dd class="mt-1 text-2xl font-bold text-primary-600"><?= $stats['total'] ?></dd>
        </div>
        <?php foreach ($stats['types'] as $type => $count): ?>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($type) ?></dt>
            <dd class="mt-1 text-2xl font-bold text-slate-900"><?= $count ?></dd>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Timeline de Insights Agregados -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Últimos Insights da Equipe</h3>
        </div>
        <div class="p-6">
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    <?php foreach ($teamInsights as $index => $insight): ?>
                    <li>
                        <div class="relative pb-8">
                            <?php if ($index !== count($teamInsights) - 1): ?>
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                            <?php endif; ?>
                            <div class="relative flex items-start space-x-3">
                                <div class="relative">
                                    <div class="h-10 w-10 rounded-full bg-primary-50 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-5 w-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"/></svg>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1 py-1.5">
                                    <div class="text-sm text-slate-500">
                                        <span class="font-bold text-slate-900"><?= htmlspecialchars($insight['user_name']) ?></span>
                                        <span class="mx-1">•</span>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold text-slate-600 uppercase">
                                            <?= htmlspecialchars($insight['insight_type']) ?>
                                        </span>
                                        <span class="ml-2 text-xs"><?= date('d/m/Y', strtotime($insight['created_at'])) ?></span>
                                    </div>
                                    <div class="mt-2 text-sm text-slate-700 italic bg-slate-50 p-3 rounded-lg border border-slate-100">
                                        "<?= htmlspecialchars($insight['content']) ?>"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    
                    <?php if (empty($teamInsights)): ?>
                    <li class="text-center py-10">
                        <p class="text-slate-400 italic">A IA ainda não processou aprendizados sobre o time. Aguarde a submissão dos primeiros relatórios.</p>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
