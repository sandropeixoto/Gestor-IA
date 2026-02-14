<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?= htmlspecialchars($title ?? 'Gestor IA', ENT_QUOTES, 'UTF-8')?>
    </title>

    <!-- TailwindCSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <?php if (isset($csrfToken)): ?>
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8')?>">
    <?php
endif; ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="h-full antialiased text-slate-900">
    <?php if (!empty($_SESSION['auth_user_id'])): ?>
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/dashboard" class="text-xl font-bold text-primary-600">Gestor IA</a>
                    </div>
                    <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                        <a href="/dashboard"
                            class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium">Dashboard</a>

                        <?php if (isset($user) && $user['role'] === 'admin'): ?>
                        <a href="/admin"
                            class="border-transparent text-indigo-600 hover:border-indigo-700 hover:text-indigo-800 inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium">
                            Admin</a>
                        <?php
    endif; ?>

                        <a href="/chat"
                            class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium">Chat
                            Assistente</a>
                        <a href="/profile"
                            class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium">Meu
                            Perfil</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <form method="post" action="/logout">
                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8')?>" />
                        <button type="submit"
                            class="text-sm font-medium text-slate-500 hover:text-slate-700">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <?php
endif; ?>
    <?= $slot ?? ''?>
</body>

</html>