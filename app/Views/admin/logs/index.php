<?php require __DIR__ . '/../../layouts/main.php'; ?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-slate-900">Logs do Sistema</h1>
            <p class="mt-2 text-sm text-slate-700">Registro recente de atividades e conversas (Chat Logs).</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a href="/admin"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Voltar
                para Dashboard</a>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-slate-300">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">
                                    Data/Hora</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                    Usuário</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                    Origem</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">
                                    Mensagem/Evento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="py-4 pl-4 pr-3 text-sm text-slate-500 sm:pl-6 text-center">Nenhum
                                    log encontrado.</td>
                            </tr>
                            <?php
else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-slate-500 sm:pl-6">
                                    <?= htmlspecialchars($log['created_at'])?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-900">
                                    <?= htmlspecialchars($log['user_name'])?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                        <?= htmlspecialchars($log['sender'] === 'user' ? 'Usuário' : 'IA')?>
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-500 max-w-xl truncate">
                                    <?= htmlspecialchars($log['message'])?>
                                </td>
                            </tr>
                            <?php
    endforeach; ?>
                            <?php
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>