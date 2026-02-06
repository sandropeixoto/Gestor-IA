<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - <?= htmlspecialchars($appConfig['name'], ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; }
        .wrap { min-height: 100vh; display: grid; place-items: center; }
        .card { width: 100%; max-width: 420px; background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 6px 20px rgba(0,0,0,.08); }
        input { width: 100%; padding: .65rem; margin-bottom: .8rem; border: 1px solid #d0d7e2; border-radius: 8px; }
        button { width: 100%; padding: .75rem; background: #2d5bff; color: #fff; border: 0; border-radius: 8px; font-weight: 700; cursor: pointer; }
        .error { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; border-radius: 8px; padding: .65rem; margin-bottom: .8rem; }
        .hint { color: #334155; font-size: .9rem; }
    </style>
</head>
<body>
<div class="wrap">
    <form class="card" method="post" action="/login">
        <h1>Gestor IA</h1>
        <p>Entre para acessar o dashboard.</p>

        <?php if ($error !== null): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" required />

        <label for="password">Senha</label>
        <input id="password" name="password" type="password" required />

        <button type="submit">Entrar</button>

        <p class="hint">Seed local: admin@gestoria.local / senha definida em <code>database/seed.sql</code>.</p>
    </form>
</div>
</body>
</html>
