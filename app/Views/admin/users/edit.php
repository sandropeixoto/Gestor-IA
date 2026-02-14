<?php require __DIR__ . '/../../layouts/main.php'; ?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">Editar
                Usuário</h2>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    <?= htmlspecialchars($_SESSION['flash_error'])?>
                </p>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
    <?php
endif; ?>

    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="/admin/users/update/<?= $user['id']?>" method="POST">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8')?>" />

                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <!-- Nome (Readonly for now) -->
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium leading-6 text-slate-900">Nome</label>
                        <div class="mt-2">
                            <input type="text" disabled value="<?= htmlspecialchars($user['name'])?>"
                                class="block w-full rounded-md border-0 py-1.5 text-slate-500 bg-slate-50 shadow-sm ring-1 ring-inset ring-slate-300 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <!-- Role (Readonly for MVP, can be editable) -->
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium leading-6 text-slate-900">Função</label>
                        <div class="mt-2">
                            <input type="text" disabled value="<?= htmlspecialchars($user['role'])?>"
                                class="block w-full rounded-md border-0 py-1.5 text-slate-500 bg-slate-50 shadow-sm ring-1 ring-inset ring-slate-300 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <!-- Manager Assignment -->
                    <div class="sm:col-span-3">
                        <label for="manager_id" class="block text-sm font-medium leading-6 text-slate-900">Gestor
                            Responsável</label>
                        <div class="mt-2">
                            <select id="manager_id" name="manager_id"
                                class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="">Selecione um gestor...</option>
                                <?php foreach ($managers as $manager): ?>
                                <?php if ($manager['id'] !== $user['id']): // Prevent self-assignment ?>
                                        <option value="<?= $manager['id']?>" <?=($user['manager_id'] ?? null) == $manager['id'] ? 'selected' : ''?>>
                                <?= htmlspecialchars($manager['name'])?> (
                                <?= htmlspecialchars($manager['email'])?>)
                                </option>
                                <?php
    endif; ?>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">O gestor terá acesso aos relatórios e evidências deste
                            usuário.</p>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-x-6">
                    <a href="/admin/users" class="text-sm font-semibold leading-6 text-slate-900">Cancelar</a>
                    <button type="submit"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Salvar
                        Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>