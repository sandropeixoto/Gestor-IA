<div class="space-y-8">
    <!-- Header de Boas-vindas e Prazos -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Olá, <?= explode(' ', $user['name'])[0] ?>! 👋</h2>
            <p class="text-slate-500 text-sm">Acompanhe seu progresso e o status dos seus relatórios.</p>
        </div>
        
        <?php if ($deadline): ?>
            <div class="flex items-center p-4 rounded-xl border <?= $isExpired ? 'bg-rose-50 border-rose-100 text-rose-700' : 'bg-amber-50 border-amber-100 text-amber-700' ?> shadow-sm">
                <div class="flex-shrink-0 mr-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-70">Prazo Final para <?= date('M/y') ?></p>
                    <p class="text-sm font-bold"><?= $isExpired ? 'EXPIRADO EM: ' : 'ENTREGAR ATÉ: ' ?><?= date('d/m/Y', strtotime($deadline)) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="flex space-x-3">
            <a href="/chat" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                Falar com Assistente
            </a>
        </div>
    </div>

    <!-- Cards de Indicadores -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card: Status do Mês -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-primary-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Status Atual (<?= date('M/y') ?>)</dt>
                            <dd class="flex items-baseline">
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
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $statusColors[$monthlyReport['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                                    <?= $statusLabels[$monthlyReport['status']] ?? $monthlyReport['status'] ?>
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Total de Relatórios -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Histórico</dt>
                            <dd class="text-2xl font-bold text-slate-900"><?= $stats['total_reports'] ?? 0 ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Pendentes Time (Se Manager/Admin) -->
        <?php if ($user['role'] !== 'employee'): ?>
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-amber-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Aguardando Revisão</dt>
                            <dd class="text-2xl font-bold text-slate-900"><?= $stats['team_submitted'] ?? 0 ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card: Área de Atuação -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Área</dt>
                            <dd class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($user['work_area'] ?? 'Não Definida') ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tabela de Relatórios Recentes -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Atividades Recentes</h3>
                    <a href="/reports" class="text-sm font-medium text-primary-600 hover:text-primary-700">Ver todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Competência</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Colaborador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php foreach ($recentReports as $rep): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?= $rep['month_year'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?= htmlspecialchars($rep['user_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $statusColors[$rep['status']] ?? 'bg-slate-100 text-slate-700' ?>">
                                        <?= $statusLabels[$rep['status']] ?? $rep['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="/reports/view/<?= $rep['id'] ?>" class="text-primary-600 hover:text-primary-900">Detalhes</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentReports)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-500 italic">Nenhuma atividade encontrada.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar do Dashboard (Atalhos/Perfil) -->
        <div class="space-y-6">
            <!-- Card Perfil Rápido -->
            <div class="bg-white shadow-sm rounded-xl border border-slate-200 p-6 text-center">
                <div class="mx-auto h-20 w-20 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-3xl font-bold mb-4">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <h4 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-sm text-slate-500 mb-6 uppercase tracking-widest"><?= $user['role'] ?></p>
                
                <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-6">
                    <a href="/profile" class="text-center group">
                        <div class="text-primary-600 group-hover:text-primary-700 font-semibold">Perfil</div>
                        <div class="text-xs text-slate-400">Editar Dados</div>
                    </a>
                    <a href="/reports" class="text-center group">
                        <div class="text-primary-600 group-hover:text-primary-700 font-semibold">Arquivo</div>
                        <div class="text-xs text-slate-400">Ver Histórico</div>
                    </a>
                </div>
            </div>

            <!-- Ajuda/Dica -->
            <div class="bg-primary-900 rounded-xl p-6 text-white shadow-lg">
                <h4 class="text-lg font-bold mb-2 flex items-center">
                    <svg class="mr-2 h-5 w-5 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Dica da IA
                </h4>
                <p class="text-primary-100 text-sm leading-relaxed">
                    Você sabia que quanto mais detalhado for seu relato no chat, melhor será o rascunho automático do seu relatório? 🚀
                </p>
            </div>
        </div>
    </div>
</div>
