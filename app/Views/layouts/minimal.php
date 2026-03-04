<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Elaboração - Gestor IA', ENT_QUOTES, 'UTF-8') ?></title>
    
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
    </style>
</head>
<body class="h-full antialiased font-sans text-slate-900 bg-slate-50">
    <div class="flex flex-col h-screen overflow-hidden">
        <!-- Minimal Top Bar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0 z-20 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="/dashboard" class="p-2 -ml-2 text-slate-400 hover:text-primary-600 transition-colors" title="Voltar ao Dashboard">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-lg font-bold text-slate-800"><?= $pageTitle ?? 'Gestor IA' ?></h1>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-slate-700"><?= htmlspecialchars($user['name']) ?></p>
                    <p class="text-[9px] text-slate-500 uppercase tracking-tighter"><?= $user['role'] ?></p>
                </div>
                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs shrink-0">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- dynamic Content Fullscreen -->
        <main class="flex-1 overflow-hidden relative">
            <?= $slot ?? '' ?>
        </main>
    </div>
</body>
</html>
