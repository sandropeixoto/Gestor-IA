<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Painel - Gestor IA', ENT_QUOTES, 'UTF-8') ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        .transition-all { transition: all 0.3s ease; }
    </style>
</head>
<body class="h-full antialiased font-sans text-slate-900 bg-slate-50" x-data="{ sidebarCollapsed: false }">
    <!-- Mobile Header -->
    <header class="md:hidden bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sticky top-0 z-40">
        <span class="text-xl font-bold text-primary-600">Gestor IA</span>
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 text-slate-500">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </header>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Desktop -->
        <aside 
            :class="sidebarCollapsed ? 'w-20' : 'w-64'"
            class="hidden md:flex flex-col flex-shrink-0 bg-white border-r border-slate-200 transition-all duration-300 z-30">
            
            <div class="flex flex-col h-full">
                <div class="h-16 flex items-center justify-between px-4 border-b border-slate-50">
                    <span x-show="!sidebarCollapsed" class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-primary-800 truncate">
                        Gestor IA
                    </span>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 mx-auto">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
                
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto font-medium">
                    <a href="<?= url('/dashboard') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed">Dashboard</span>
                    </a>
                    <a href="<?= url('/chat') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Chat' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span x-show="!sidebarCollapsed">Chat Assistente</span>
                    </a>
                    <a href="<?= url('/reports') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Relatórios' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="!sidebarCollapsed">Relatórios</span>
                    </a>

                    <?php if ($auth->isManager()): ?>
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-2 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gestão</div>
                    <a href="<?= url('/team') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Time' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="!sidebarCollapsed">Meu Time</span>
                    </a>
                    <a href="<?= url('/team/insights') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Insights' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.989-2.386l-.548-.547z"/></svg>
                        <span x-show="!sidebarCollapsed">Insights do Time</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($user['role'] === 'admin'): ?>
                    <div x-show="!sidebarCollapsed" class="pt-4 pb-2 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Admin</div>
                    <a href="<?= url('/admin/users') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Usuários' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="!sidebarCollapsed">Usuários</span>
                    </a>
                    <a href="<?= url('/admin/personas') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors" :title="sidebarCollapsed ? 'Personas' : ''">
                        <svg class="h-5 w-5" :class="sidebarCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-show="!sidebarCollapsed">Personas de IA</span>
                    </a>
                    <?php endif; ?>
                </nav>

                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    <a href="<?= url('/profile') ?>" class="flex items-center group">
                        <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs shrink-0">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <div class="ml-3 overflow-hidden" x-show="!sidebarCollapsed">
                            <p class="text-xs font-bold text-slate-700 truncate"><?= htmlspecialchars($user['name']) ?></p>
                            <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                <?php 
                                    if ($user['role'] === 'admin') echo 'Administrador';
                                    elseif ($auth->isManager()) echo 'Gestor';
                                    else echo 'Colaborador';
                                ?>
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0 bg-slate-50 overflow-hidden">
            <!-- Top Header Desktop -->
            <header class="hidden md:flex h-16 bg-white border-b border-slate-200 items-center justify-between px-8 shrink-0 z-20 shadow-sm">
                <h1 class="text-lg font-bold text-slate-800"><?= $pageTitle ?? 'Dashboard' ?></h1>
                
                <div class="flex items-center gap-6">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 text-slate-400 hover:text-primary-600 transition-colors relative">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span id="notif-count" class="hidden absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden">
                            <div class="p-3 bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Notificações</div>
                            <div id="notif-list" class="max-h-96 overflow-y-auto divide-y divide-slate-100 text-sm">
                                <div class="p-4 text-center text-slate-400">Carregando...</div>
                            </div>
                        </div>
                    </div>

                    <form action="<?= url('/logout') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <button type="submit" class="text-xs font-bold text-slate-500 hover:text-rose-600 uppercase transition-colors flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sair
                        </button>
                    </form>
                </div>
            </header>

            <!-- dynamic Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <?= $slot ?? '' ?>
            </main>
        </div>
    </div>

    <script>
        // Orion Debug - Dados do Usuário
        console.log('👤 Usuário Logado:', <?= json_encode($user) ?>);

        async function loadNotifications() {
            const list = document.getElementById('notif-list');
            const count = document.getElementById('notif-count');
            try {
                // Descobre a base URL dinamicamente
                const basePath = '<?= url('') ?>';
                const response = await fetch(`${basePath}/api/notifications`);
                const data = await response.json();
                if (data.unread_count > 0) count.classList.remove('hidden');
                else count.classList.add('hidden');

                if (!data.notifications || data.notifications.length === 0) {
                    list.innerHTML = '<div class="p-8 text-center text-slate-400 text-xs italic">Nenhum alerta.</div>';
                    return;
                }

                list.innerHTML = data.notifications.map(n => `
                    <div class="p-4 hover:bg-slate-50 cursor-pointer ${n.is_read ? 'opacity-50' : ''}" onclick="window.location.href='${n.link || '#'}'">
                        <p class="text-xs font-bold text-slate-900">${n.title}</p>
                        <p class="text-[10px] text-slate-600 mt-1">${n.message}</p>
                    </div>
                `).join('');
            } catch (e) { console.error(e); }
        }
        window.addEventListener('load', loadNotifications);
    </script>
</body>
</html>
