<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($appConfig['name'], ENT_QUOTES, 'UTF-8') ?> - Bootstrap</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; background: #f7f8fa; }
        .card { background: #fff; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,.08); max-width: 860px; }
        code { background: #eef2ff; padding: .15rem .35rem; border-radius: 4px; }
        .ok { color: #0a7a2f; font-weight: 700; }
        .warn { color: #a65d03; font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        <h1><?= htmlspecialchars($appConfig['name'], ENT_QUOTES, 'UTF-8') ?> (MVP)</h1>
        <p>Estrutura inicial criada com front controller, configuração por ambiente e teste de conexão com banco.</p>

        <h2>Status inicial</h2>
        <ul>
            <li><strong>Ambiente:</strong> <code><?= htmlspecialchars($appConfig['env'], ENT_QUOTES, 'UTF-8') ?></code></li>
            <li><strong>URL base:</strong> <code><?= htmlspecialchars($appConfig['url'], ENT_QUOTES, 'UTF-8') ?></code></li>
            <li><strong>Uploads:</strong> <code><?= htmlspecialchars($appConfig['upload_dir'], ENT_QUOTES, 'UTF-8') ?></code></li>
            <li><strong>Banco:</strong> <span class="<?= str_starts_with($dbStatus, 'Conexão') ? 'ok' : 'warn' ?>"><?= htmlspecialchars($dbStatus, ENT_QUOTES, 'UTF-8') ?></span></li>
        </ul>

        <h2>Próximo passo</h2>
        <p>Implementar autenticação e autorização por perfis (<code>admin</code>, <code>manager</code>, <code>employee</code>).</p>
    </div>
</body>
</html>
