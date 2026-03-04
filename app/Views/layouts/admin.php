<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Painel - Gestor IA', ENT_QUOTES, 'UTF-8') ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8',
                            500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-item-active { background-color: #f1f5f9; color: #0f172a; border-right: 4px solid #0284c7; }
    </style>
</head>
<body class="h-full antialiased font-sans text-slate-900">
    <div class="min-h-full flex">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-white border-r border-slate-200">
            <div class="flex flex-col flex-grow pt-5 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4 mb-8">
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-primary-800">
                        Gestor IA
                    </span>
                </div>
                
                <nav class="flex-1 px-2 space-y-1">
                    <!-- Links Comuns -->
                    <a href="/dashboard" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    <a href="/chat" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Mentoria IA
                    </a>

                    <a href="/reports" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Relatórios
                    </a>

                    <?php if ($user['role'] === 'manager' || $user['role'] === 'admin'): ?>
                    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Gestão</div>
                    <a href="/team" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Meu Time
                    </a>
                    <a href="/team/insights" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"/></svg>
                        Insights do Time
                    </a>
                    <?php endif; ?>

                    <?php if ($user['role'] === 'admin'): ?>
                    <a href="/admin/users" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Usuários
                    </a>
                    <a href="/admin/personas" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Personas de IA
                    </a>
                    <a href="/admin/logs" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Logs do Sistema
                    </a>
                    <?php endif; ?>
                </nav>

                <div class="flex-shrink-0 flex border-t border-slate-200 p-4">
                    <a href="/profile" class="flex-shrink-0 w-full group block">
                        <div class="flex items-center">
                            <div class="inline-block h-9 w-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-slate-700 group-hover:text-slate-900"><?= htmlspecialchars($user['name']) ?></p>
                                <p class="text-xs font-medium text-slate-500 group-hover:text-slate-700 uppercase"><?= $user['role'] ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 md:pl-64">
            <header class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white border-b border-slate-200">
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex items-center">
                        <h1 class="text-lg font-semibold text-slate-900"><?= $pageTitle ?? 'Dashboard' ?></h1>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        <!-- Notificações Dropdown -->
                        <div class="relative" id="notification-dropdown">
                            <button onclick="toggleNotifications()" class="p-2 rounded-full text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition-all relative">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span id="notif-count" class="hidden absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                            </button>
                            
                            <div id="notif-panel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Notificações</span>
                                </div>
                                <div id="notif-list" class="max-h-96 overflow-y-auto divide-y divide-slate-100">
                                    <div class="p-4 text-center text-slate-400 text-sm">Carregando...</div>
                                </div>
                            </div>
                        </div>

                        <form method="post" action="/logout">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>" />
                            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700 flex items-center">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <div class="py-6 px-4 sm:px-6 md:px-8">
                    <?= $slot ?? '' ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleNotifications() {
            const panel = document.getElementById('notif-panel');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                loadNotifications();
            }
        }

        async function loadNotifications() {
            const list = document.getElementById('notif-list');
            const count = document.getElementById('notif-count');
            
            try {
                const response = await fetch('/api/notifications');
                const data = await response.json();
                
                if (data.unread_count > 0) {
                    count.classList.remove('hidden');
                } else {
                    count.classList.add('hidden');
                }

                if (data.notifications.length === 0) {
                    list.innerHTML = '<div class="p-8 text-center text-slate-400 text-sm italic">Nenhum alerta recente.</div>';
                    return;
                }

                list.innerHTML = data.notifications.map(n => `
                    <div class="p-4 hover:bg-slate-50 transition-colors ${n.is_read ? 'opacity-60' : 'bg-primary-50/30'}" onclick="markRead(${n.id}, '${n.link}')">
                        <p class="text-sm font-bold text-slate-900">${n.title}</p>
                        <p class="text-xs text-slate-600 mt-1">${n.message}</p>
                        <p class="text-[10px] text-slate-400 mt-2 uppercase font-bold tracking-tighter">${new Date(n.created_at).toLocaleDateString()}</p>
                    </div>
                `).join('');
            } catch (e) {
                list.innerHTML = '<div class="p-4 text-rose-500 text-xs">Erro ao carregar notificações.</div>';
            }
        }

        async function markRead(id, link) {
            await fetch(\`/api/notifications/read/${id}\`, { method: 'POST' });
            if (link && link !== 'null') {
                window.location.href = link;
            } else {
                loadNotifications();
            }
        }

        // Auto-load initial count
        window.addEventListener('load', async () => {
            const response = await fetch('/api/notifications');
            const data = await response.json();
            if (data.unread_count > 0) document.getElementById('notif-count').classList.remove('hidden');
        });
    </script>
</body>
</html>
