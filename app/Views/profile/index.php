<?php require __DIR__ . '/../layouts/main.php'; ?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">Meu Perfil</h2>
        </div>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($flashSuccess) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
        <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($flashError) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="/profile/update" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <!-- Nome (Readonly) -->
                    <div class="sm:col-span-3">
                        <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Nome Completo</label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" disabled value="<?= htmlspecialchars($user['name']) ?>" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-slate-50">
                        </div>
                    </div>

                    <!-- Email (Readonly) -->
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Email Corporativo</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" disabled value="<?= htmlspecialchars($user['email']) ?>" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-slate-50">
                        </div>
                    </div>

                    <!-- Work Area -->
                    <div class="sm:col-span-3">
                        <label for="work_area" class="block text-sm font-medium leading-6 text-slate-900">Área de Atuação</label>
                        <p class="mt-1 text-sm text-slate-500">Define o "tom de voz" e o foco da IA.</p>
                        <div class="mt-2">
                            <select id="work_area" name="work_area" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <?php
                                $areas = ['TI', 'Administrativo', 'Financeiro', 'Jurídico', 'RH', 'Obras', 'Geral'];
                                foreach ($areas as $area): ?>
                                    <option value="<?= $area ?>" <?= ($user['work_area'] ?? '') === $area ? 'selected' : '' ?>><?= $area ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Role Description -->
                    <div class="col-span-full">
                        <label for="role_description" class="block text-sm font-medium leading-6 text-slate-900">Descrição das Atividades (Mentoria)</label>
                        <div class="mt-2">
                            <textarea id="role_description" name="role_description" rows="5" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Ex: Sou responsável por controlar contas a pagar, emitir notas fiscais e fazer a conciliação bancária diária..."><?= htmlspecialchars($user['role_description'] ?? '') ?></textarea>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Descreva o que você faz no dia a dia. Se deixar em branco, a IA fará perguntas durante o chat para "aprender" o seu papel.
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-x-6">
                    <a href="/dashboard" class="text-sm font-semibold leading-6 text-slate-900">Cancelar</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Salvar Perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
