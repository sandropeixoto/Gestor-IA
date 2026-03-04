<div class="space-y-8">
    <!-- Header de Perfil -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-primary-600 to-primary-800"></div>
        <div class="px-6 pb-6">
            <div class="relative flex items-end -mt-12 mb-4">
                <div class="h-24 w-24 rounded-full bg-white p-1 shadow-lg">
                    <div class="h-full w-full rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-3xl font-bold">
                        <?= strtoupper(substr($targetUser['name'], 0, 1)) ?>
                    </div>
                </div>
                <div class="ml-6 mb-2">
                    <h2 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($targetUser['name']) ?></h2>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-widest"><?= $targetUser['role'] ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <dt class="text-xs font-bold text-slate-400 uppercase">Email Corporativo</dt>
                    <dd class="text-sm text-slate-700"><?= htmlspecialchars($targetUser['email']) ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-slate-400 uppercase">Área de Atuação</dt>
                    <dd class="text-sm text-slate-700"><?= htmlspecialchars($targetUser['work_area'] ?? 'Não informada') ?></dd>
                </div>
                <div>
                    <dt class="text-xs font-bold text-slate-400 uppercase">Descrição do Cargo</dt>
                    <dd class="text-sm text-slate-700"><?= htmlspecialchars($targetUser['role_description'] ?? 'Não informada') ?></dd>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Histórico de Relatórios -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Histórico de Relatórios</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Competência</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php foreach ($recentReports as $rep): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?= $rep['month_year'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php
                                        $statusColors = [
                                            'draft' => 'bg-amber-100 text-amber-700',
                                            'submitted' => 'bg-blue-100 text-blue-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                        ];
                                    ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $statusColors[$rep['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                                        <?= strtoupper($rep['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/reports/view/<?= $rep['id'] ?>" class="text-primary-600 hover:text-primary-900">Ver Relatório</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Memória da IA (Insights Individuais) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Aprendizados da IA</h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php foreach ($userInsights as $insight): ?>
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded-full bg-primary-100 text-[10px] font-bold text-primary-700 uppercase">
                                <?= htmlspecialchars($insight['insight_type']) ?>
                            </span>
                            <span class="text-[10px] text-slate-400 font-bold"><?= date('d/m/y', strtotime($insight['created_at'])) ?></span>
                        </div>
                        <p class="text-xs text-slate-600 italic leading-relaxed">
                            "<?= htmlspecialchars($insight['content']) ?>"
                        </p>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($userInsights)): ?>
                    <p class="text-center text-slate-400 text-xs italic py-4">Nenhum insight gerado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
