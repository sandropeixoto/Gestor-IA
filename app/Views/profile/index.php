<?php require __DIR__ . '/../layouts/main.php'; ?>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900">Meu Perfil</h2>
        <p class="mt-2 text-sm text-slate-600">Gerencie suas informações e veja o que a IA aprendeu sobre você.</p>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="rounded-lg bg-green-50 p-4 mb-6 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($flashSuccess) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
        <div class="rounded-lg bg-red-50 p-4 mb-6 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($flashError) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-3">
        
        <!-- Column 1: Configurações de Perfil (2/3 width) -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-4 py-6 sm:p-8">
                    <form action="/profile/update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                        
                        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <!-- Header Info -->
                            <div class="col-span-full border-b border-slate-900/10 pb-8">
                                <h2 class="text-base font-semibold leading-7 text-slate-900">Informações Pessoais</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">Dados básicos da sua conta.</p>

                                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium leading-6 text-slate-900">Nome Completo</label>
                                        <div class="mt-2">
                                            <input type="text" disabled value="<?= htmlspecialchars($user['name']) ?>" class="block w-full rounded-md border-0 py-1.5 text-slate-500 shadow-sm ring-1 ring-inset ring-slate-300 bg-slate-50 sm:text-sm sm:leading-6 cursor-not-allowed">
                                        </div>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Email Corporativo</label>
                                        <div class="mt-2">
                                            <input type="email" disabled value="<?= htmlspecialchars($user['email']) ?>" class="block w-full rounded-md border-0 py-1.5 text-slate-500 shadow-sm ring-1 ring-inset ring-slate-300 bg-slate-50 sm:text-sm sm:leading-6 cursor-not-allowed">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contexto da IA -->
                            <div class="col-span-full pt-2">
                                <div class="flex items-center gap-x-3">
                                    <div class="p-2 bg-indigo-50 rounded-lg">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-base font-semibold leading-7 text-slate-900">Personalização da IA</h2>
                                </div>
                                <p class="mt-1 text-sm leading-6 text-slate-500 mb-6 border-b border-slate-900/10 pb-6">
                                    Essas informações ajudam o Assistente a gerar respostas mais precisas para o seu contexto.
                                </p>

                                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="sm:col-span-3">
                                        <label for="work_area" class="block text-sm font-medium leading-6 text-slate-900">Área de Atuação</label>
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

                                    <div class="col-span-full">
                                        <label for="role_description" class="block text-sm font-medium leading-6 text-slate-900">
                                            Descrição das Atividades
                                            <span class="ml-2 inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Importante</span>
                                        </label>
                                        <div class="mt-2">
                                            <textarea id="role_description" name="role_description" rows="6" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 text-base" placeholder="Ex: Sou responsável por controlar contas a pagar, emitir notas fiscais e fazer a conciliação bancária diária..."><?= htmlspecialchars($user['role_description'] ?? '') ?></textarea>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-500">
                                            Se você não preencher, a IA fará perguntas durante o chat para aprender sobre seu trabalho.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-x-6 border-t border-slate-900/10 pt-6">
                            <a href="/dashboard" class="text-sm font-semibold leading-6 text-slate-900">Cancelar</a>
                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Salvar Mudanças</button>
                            </div>
                            
                            <!-- Gestor Responsável -->
                            <div class="col-span-full pt-6 border-t border-slate-900/10">
                                <h2 class="text-base font-semibold leading-7 text-slate-900">Seu Gestor</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Quem é o seu líder direto? Isso permite que ele veja seus relatórios.
                                </p>

                                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                                    <div class="sm:col-span-4">
                                        <?php 
                                            // Get current manager info if exists. Need to fetch it or rely on user data having it.
                                            // Ideally we pass $managerName or $managerEmail to view.
                                            // For MVP, since user array doesn't have joined manager info by default in findById unless we updated UserModel
                                            // Let's assume we need to update ProfileController to pass manager info or fetch it here.
                                            // ProfileController index calls findByUserId (user model). UserModel::findById DOES NOT JOIN manager name yet.
                                            // But wait, UserModel::getAllUsers DOES join it.
                                            // Let's rely on ProfileController passing it or just show email if we have ID.
                                            // Actually, the user array from auth->user() might be stale session data. 
                                            // ProfileController calls $auth->user(), which refreshes from DB if ID is set.
                                            // UserModel::findById returns 'manager_id'.
                                            // We need to fetch manager details to show name/email.
                                            
                                            // Hack for now: We will implement the form actions. showing current manager is nice-to-have but user asked for assignment flow.
                                            // Let's implement the assignment form and maybe a small script to show current if known.
                                        ?>
                                        
                                        <!-- Separate Form for Manager Assignment? No, user wants simple flow. -->
                                        <!-- But the main form goes to /profile/update. Manager assignment goes to /profile/assign-manager? -->
                                        <!-- Let's keep them separate or use JS. Separate form is cleaner for implementation. -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Separate Form for Manager Assignment -->
                    <div class="mt-8 pt-8 border-t border-slate-900/10">
                        <form action="/profile/assign-manager" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                            <h2 class="text-base font-semibold leading-7 text-slate-900">Definir Gestor</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Informe o email do seu gestor para vincular sua conta.
                            </p>
                            
                            <div class="mt-4 flex gap-x-4">
                                <label for="manager_email" class="sr-only">Email do Gestor</label>
                                <input id="manager_email" name="manager_email" type="email" required class="min-w-0 flex-auto rounded-md border-0 px-3.5 py-2 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="email.do.gestor@empresa.com">
                                <button type="submit" class="flex-none rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Conectar</button>
                            </div>
                            <?php if (!empty($user['manager_id'])): ?>
                                <p class="mt-2 text-sm text-green-600">
                                    <span class="font-medium">Status:</span> Você já possui um gestor vinculado (ID: <?= $user['manager_id'] ?>).
                                </p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Memória da IA (1/3 width) -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-4 py-6 sm:px-6">
                    <h3 class="text-base font-semibold leading-7 text-slate-900 flex items-center gap-2">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.854 1.591-2.16 1.115-.366 1.909-1.42 1.909-2.6 0-1.782-1.638-3-3.75-3s-3.75 1.218-3.75 3c0 1.18.795 2.235 1.909 2.6.933.306 1.591 1.177 1.591 2.16V18" />
                        </svg>
                        Memória da IA
                    </h3>
                    <p class="mt-1 text-sm leading-6 text-slate-500">O que o assistente aprendeu sobre você através das conversas.</p>
                </div>
                <div class="border-t border-slate-100">
                    <?php if (empty($insights)): ?>
                        <div class="px-4 py-8 text-center text-sm text-slate-500">
                            <p>Nenhuma memória registrada ainda.</p>
                            <p class="mt-1">Converse com o assistente para gerar aprendizados.</p>
                        </div>
                    <?php else: ?>
                        <ul role="list" class="divide-y divide-slate-100">
                            <?php foreach ($insights as $insight): ?>
                                <li class="px-4 py-4 sm:px-6 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-start gap-x-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-slate-900"><?= htmlspecialchars($insight['insight_type']) ?></p>
                                            <p class="mt-1 text-sm text-slate-600 leading-relaxed"><?= htmlspecialchars($insight['content']) ?></p>
                                            <p class="mt-2 text-xs text-slate-400">
                                                Aprendido em <?= date('d/m/Y H:i', strtotime($insight['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <!-- Future 'Clear Memory' Action -->
                <!-- <div class="border-t border-slate-100 px-4 py-4 sm:px-6">
                    <button type="button" class="text-xs font-semibold text-slate-900 hover:text-indigo-600">Limpar histórico de aprendizado</button>
                </div> -->
            </div>
        </div>
        
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
