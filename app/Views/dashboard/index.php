<?php
$title = 'Dashboard - ' . ($appConfig['name'] ?? 'Gestor IA');
ob_start();
?>

<div class="min-h-screen bg-slate-50">
    <nav class="bg-white border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <span class="text-xl font-bold text-slate-900 tracking-tight">Gestor IA</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-slate-600 hidden sm:block">
                        Olá, <span class="font-semibold text-slate-900">
                            <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8')?>
                        </span>
                        <span
                            class="ml-2 inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8')?>
                        </span>
                    </div>
                    <form method="post" action="/logout">
                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8')?>" />
                        <button type="submit"
                            class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-10">
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold leading-tight tracking-tight text-slate-900">Dashboard</h1>
                <a href="/chat"
                    class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 2c-1.716 0-3.408.106-5.07.31C3.806 2.45 3 3.414 3 4.517V17.25a.75.75 0 001.075.676L10 15.082l5.925 2.844A.75.75 0 0017 17.25V4.517c0-1.103-.806-2.068-1.93-2.207A41.403 41.403 0 0010 2z"
                            clip-rule="evenodd" />
                    </svg>
                    Ir para Chat
                </a>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 mt-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Area Selector Card -->
                    <div class="overflow-hidden rounded-xl bg-white border border-slate-200 shadow-sm col-span-1 sm:col-span-2 lg:col-span-3">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-base font-semibold leading-6 text-slate-900">Personalização da IA</h3>
                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Novo</span>
                        </div>
                        <div class="p-6">
                            <form action="/dashboard/update-profile" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                                <div class="w-full sm:max-w-xs">
                                    <label for="work_area" class="block text-sm font-medium leading-6 text-slate-900">Sua Área de Atuação</label>
                                    <div class="mt-2">
                                        <select id="work_area" name="work_area" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                                            <option value="" disabled <?= empty($user['work_area']) ? 'selected' : '' ?>>Selecione...</option>
                                            <?php
                                            $areas = ['TI', 'Administrativo', 'Financeiro', 'Jurídico', 'RH', 'Obras', 'Geral'];
                                            foreach ($areas as $area): ?>
                                                <option value="<?= $area ?>" <?= ($user['work_area'] ?? '') === $area ? 'selected' : '' ?>><?= $area ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                    Atualizar Contexto
                                </button>
                            </form>
                            <p class="mt-2 text-sm text-slate-500">Isso ajuda a IA a usar termos técnicos e focar no que importa para sua área.</p>
                        </div>
                    </div>

                    <!-- Card 1: Ciclo Mensal -->
                    <div class="overflow-hidden rounded-xl bg-white border border-slate-200 shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100">
                                        <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-slate-500">Competência</dt>
                                        <dd>
                                            <div class="text-lg font-medium text-slate-900">
                                                <?= htmlspecialchars($currentMonthYear, ENT_QUOTES, 'UTF-8')?>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-3">
                            <div class="text-sm">
                                <span class="font-medium text-slate-900">Relatório #
                                    <?=(int)$monthlyReport['id']?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Status -->
                    <div class="overflow-hidden rounded-xl bg-white border border-slate-200 shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="truncate text-sm font-medium text-slate-500">Status atual</dt>
                                        <dd>
                                            <?php
// Mapping status to colors
$statusColors = [
    'draft' => 'text-yellow-700 bg-yellow-50 ring-yellow-600/20',
    'submitted' => 'text-blue-700 bg-blue-50 ring-blue-700/10',
    'approved' => 'text-green-700 bg-green-50 ring-green-600/20',
    'rejected' => 'text-red-700 bg-red-50 ring-red-600/10',
];
$currentStatus = (string)$monthlyReport['status'];
$badgeClass = $statusColors[$currentStatus] ?? 'text-gray-600 bg-gray-50 ring-gray-500/10';

// Using Enum label if existing, else string
$statusLabel = match ($currentStatus) {
        'draft' => 'Rascunho',
        'submitted' => 'Enviado',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        default => $currentStatus,
    };
?>
                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset <?= $badgeClass?>">
                                                <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8')?>
                                            </span>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-6 py-3">
                            <div class="text-sm text-slate-500 truncate">
                                Atualizado:
                                <?= htmlspecialchars((string)$monthlyReport['updated_at'], ENT_QUOTES, 'UTF-8')?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Debug Area -->
                <div class="mt-8 overflow-hidden rounded-xl bg-white border border-slate-200 shadow-sm">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="text-base font-semibold leading-6 text-slate-900">Diagnóstico de Permissões</h3>
                    </div>
                    <div class="px-6 py-5">
                        <ul role="list" class="divide-y divide-slate-100 rounded-md border border-slate-200">
                            <li class="flex items-center justify-between py-3 pl-3 pr-4 text-sm">
                                <div class="flex w-0 flex-1 items-center">
                                    <span class="ml-2 w-0 flex-1 truncate">Usuário autenticado</span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span class="font-medium text-green-600">Sim</span>
                                </div>
                            </li>
                            <li class="flex items-center justify-between py-3 pl-3 pr-4 text-sm">
                                <div class="flex w-0 flex-1 items-center">
                                    <span class="ml-2 w-0 flex-1 truncate">Acesso ao usuário #
                                        <?= $sampleTargetUserId?> (Demo)
                                    </span>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span
                                        class="font-medium <?= $canViewSampleEmployee ? 'text-green-600' : 'text-red-600'?>">
                                        <?= $canViewSampleEmployee ? 'Permitido' : 'Negado'?>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>