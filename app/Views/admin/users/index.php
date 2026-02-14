<?php require __DIR__ . '/../../layouts/main.php'; ?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-slate-900">Gerenciamento de Usuários</h1>
            <p class="mt-2 text-sm text-slate-700">Lista de todos os usuários, suas funções e gestores responsáveis.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <!-- Future: Add User Button -->
        </div>
    </div>

    <?php if ($flashSuccess): ?>
    <div class="mt-4 rounded-md bg-green-50 p-4 border border-green-200">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    <?= htmlspecialchars($flashSuccess)?>
                </p>
            </div>
        </div>
    </div>
    <?php
endif; ?>

    <?php if ($flashError): ?>
    <div class="mt-4 rounded-md bg-red-50 p-4 border border-red-200">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    <?= htmlspecialchars($flashError)?>
                </p>
            </div>
        </div>
    </div>
    <?php
endif; ?>

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-slate-300">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-0">Nome
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Email
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Função
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Gestor
                            </th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                <span class="sr-only">Editar</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-0">
                                <?= htmlspecialchars($user['name'])?>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                <?= htmlspecialchars($user['email'])?>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                <span
                                    class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-600/20">
                                    <?= htmlspecialchars($user['role'])?>
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                <?php if (!empty($user['manager_name'])): ?>
                                <span
                                    class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                    <?= htmlspecialchars($user['manager_name'])?>
                                </span>
                                <?php
    else: ?>
                                <span class="text-slate-400 italic">Nenhum</span>
                                <?php
    endif; ?>
                            </td>
                            <td
                                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                <a href="/admin/users/edit/<?= $user['id']?>"
                                    class="text-indigo-600 hover:text-indigo-900">Editar<span class="sr-only">,
                                        <?= htmlspecialchars($user['name'])?>
                                    </span></a>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>