<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard -
        <?= htmlspecialchars($appConfig['name'], ENT_QUOTES, 'UTF-8')?>
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            margin: 0;
        }

        .container {
            max-width: 920px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .07);
        }

        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: .2rem .6rem;
            border-radius: 999px;
            font-size: .85rem;
        }

        .ok {
            color: #166534;
            font-weight: 700;
        }

        .no {
            color: #991b1b;
            font-weight: 700;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button {
            padding: .6rem .8rem;
            border: 0;
            background: #111827;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="top">
            <h1>Dashboard</h1>
            <a href="/chat" style="margin-left:1rem;">Ir para Chat</a>
            <form method="post" action="/logout"><input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8')?>" /><button
                    type="submit">Sair</button></form>
        </div>
        <p>Bem-vindo, <strong>
                <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8')?>
            </strong> <span class="badge">
                <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8')?>
            </span></p>


        <h2>Ciclo mensal (item 3)</h2>
        <ul>
            <li>Competência atual: <strong>
                    <?= htmlspecialchars($currentMonthYear, ENT_QUOTES, 'UTF-8')?>
                </strong></li>
            <li>ID do relatório: <strong>#
                    <?=(int)$monthlyReport['id']?>
                </strong></li>
            <li>Status: <strong>
                    <?= htmlspecialchars((string)$monthlyReport['status'], ENT_QUOTES, 'UTF-8')?>
                </strong></li>
            <li>Atualizado em: <strong>
                    <?= htmlspecialchars((string)$monthlyReport['updated_at'], ENT_QUOTES, 'UTF-8')?>
                </strong></li>
        </ul>

        <h2>Controle de acesso (item 2)</h2>
        <ul>
            <li>Usuário autenticado: <span class="ok">sim</span></li>
            <li>Pode visualizar dados do usuário #
                <?= $sampleTargetUserId?>: <span class="<?= $canViewSampleEmployee ? 'ok' : 'no'?>">
                    <?= $canViewSampleEmployee ? 'sim' : 'não'?>
                </span>
            </li>
        </ul>
    </div>
</body>

</html>