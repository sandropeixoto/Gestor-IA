<div class="space-y-8">
    <!-- Header e Prazo -->
    <div class="md:flex md:items-center md:justify-between bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Gestão do Time</h2>
            <p class="text-slate-500 text-sm">Acompanhe o engajamento e a evolução dos seus liderados diretos.</p>
        </div>
        <div class="mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 border-slate-100">
            <form action="<?= url('/team/deadline') ?>" method="POST" class="flex flex-col sm:flex-row items-end sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="w-full sm:w-auto">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Prazo de Entrega (<?= date('M/y') ?>)</label>
                    <input type="date" name="deadline_date" value="<?= $currentDeadline ?? '' ?>" class="block w-full rounded-lg border-slate-300 text-xs focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-primary-600 text-white text-xs font-bold rounded-lg hover:bg-primary-700 transition-colors">
                    Salvar Prazo
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Liderados -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($team as $member): ?>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-lg">
                            <?= strtoupper(substr($member['name'], 0, 1)) ?>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-bold text-slate-900"><?= htmlspecialchars($member['name']) ?></h3>
                            <p class="text-xs text-slate-500 italic"><?= htmlspecialchars($member['work_area'] ?? 'Área não definida') ?></p>
                        </div>
                    </div>
                    <?php if ($member['report_status']): ?>
                        <?php
                            $statusColors = [
                                'draft' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            ];
                        ?>
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold border <?= $statusColors[$member['report_status']] ?? 'bg-slate-100 text-slate-700' ?>">
                            <?= strtoupper($member['report_status']) ?>
                        </span>
                    <?php else: ?>
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold border bg-rose-50 text-rose-600 border-rose-100">
                            PENDENTE
                        </span>
                    <?php endif; ?>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Relatório de <?= date('M/y') ?></span>
                        <span class="font-medium text-slate-900">
                            <?= $member['report_updated_at'] ? date('d/m H:i', strtotime($member['report_updated_at'])) : '--' ?>
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-primary-500 h-1.5 rounded-full" style="width: <?= $member['report_status'] ? ($member['report_status'] === 'approved' ? '100%' : '50%') : '0%' ?>"></div>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between items-center">
                <a href="<?= url('/team/user/' . $member['id']) ?>" class="text-xs font-bold text-primary-600 hover:text-primary-800 uppercase tracking-wider">Ver Perfil</a>
                <?php if ($member['report_id']): ?>
                    <a href="<?= url('/reports/view/' . $member['id']) ?>" class="text-xs font-bold text-slate-500 hover:text-slate-700 uppercase tracking-wider">Avaliar</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($team)): ?>
        <div class="col-span-full py-20 text-center bg-white rounded-xl border-2 border-dashed border-slate-200">
            <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <p class="text-slate-500 font-medium">Nenhum liderado vinculado a você no momento.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
