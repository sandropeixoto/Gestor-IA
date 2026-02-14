<?php
$title = 'Login - ' . ($appConfig['name'] ?? 'Gestor IA');
ob_start();
?>
<div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-slate-900">Gestor IA</h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Entre para acessar o dashboard
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-xl sm:px-10 border border-slate-100">
            <?php if ($error !== null): ?>
            <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon: x-circle -->
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erro de autenticação</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
endif; ?>

            <form class="space-y-6" action="/login" method="POST">
                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8')?>" />

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-900">E-mail</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="block w-full rounded-lg border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Senha</label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full rounded-lg border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-colors">
                        Entrar
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white px-2 text-slate-500">Credenciais Demo</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3">
                    <p class="text-xs text-center text-slate-500 bg-slate-50 p-2 rounded border border-slate-100">
                        admin@gestoria.local / Admin@123<br>
                        Confira <code>database/seed.sql</code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>