<?php

/**
 * view do dashboard.
 * espera receber do DashboardController: $loggedUser, $services,
 * $totalValue, $pendingServices, $today.
 */

function formatMoney(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function formatDate(?string $datetime): string
{
    if (empty($datetime)) {
        return '-';
    }

    return date('d/m/Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Ordem de Serviços</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            color: #1a1a1a;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #4a4a4a;
            color: #fff;
            padding: 24px 20px;
            flex-shrink: 0;
        }

        .sidebar p {
            margin: 0 0 4px;
        }

        .sidebar .user-name {
            font-weight: bold;
            margin-bottom: 24px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .sidebar a:hover {
            text-decoration: underline;
        }

        .sidebar .logout {
            margin-top: 40px;
            font-weight: normal;
            color: #ddd;
        }

        .content {
            flex: 1;
            padding: 32px 40px;
        }

        h1 {
            font-size: 28px;
            margin: 0 0 24px;
        }

        .summary-row {
            display: flex;
            gap: 40px;
            margin-bottom: 32px;
        }

        .summary-box h2 {
            font-size: 20px;
            margin: 0 0 12px;
        }

        .summary-box.total .value {
            font-size: 26px;
            font-weight: bold;
            color: #15803d;
        }

        .summary-box ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .summary-box li {
            margin-bottom: 6px;
        }

        form.filters {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            align-items: center;
        }

        form.filters input,
        form.filters select {
            padding: 10px 12px;
            border: 1px solid #999;
            border-radius: 2px;
            font-size: 14px;
        }

        form.filters button {
            padding: 10px 20px;
            background: #4a4a4a;
            color: #fff;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            font-weight: bold;
        }

        form.filters button:hover {
            background: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }

        table th {
            font-size: 13px;
            text-transform: uppercase;
            color: #555;
        }

        .status {
            font-size: 12px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 10px;
        }

        .status.pendente {
            background: #fef3c7;
            color: #92400e;
        }

        .status.finalizado {
            background: #dcfce7;
            color: #166534;
        }

        .actions form {
            display: inline;
        }

        .actions button {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 13px;
            margin-right: 8px;
            color: #2563eb;
        }

        .actions button.delete {
            color: #b91c1c;
        }

        .empty {
            padding: 20px 0;
            color: #777;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <p>Logado como:</p>
        <p class="user-name"><?= htmlspecialchars($loggedUser['name']) ?></p>

        <a href="servico_cadastrar.php">Cadastrar Serviço</a>

        <a class="logout" href="logout.php">Sair</a>
    </aside>

    <main class="content">
        <h1>DASHBOARD <small style="font-size: 14px; color: #777;"><?= htmlspecialchars($today) ?></small></h1>

        <div class="summary-row">
            <div class="summary-box total">
                <h2>Valor Total dos Serviços</h2>
                <div class="value"><?= formatMoney($totalValue) ?></div>
            </div>

            <div class="summary-box">
                <h2>Serviços Pendentes</h2>
                <?php if (empty($pendingServices)): ?>
                    <p class="empty">Nenhum serviço pendente.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($pendingServices as $pending): ?>
                            <li><?= (int) $pending['id_service'] ?> - <?= htmlspecialchars($pending['description']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <form class="filters" method="GET" action="dashboard.php">
            <input type="text" name="description" placeholder="Nome do serviço" value="<?= htmlspecialchars($filters['description'] ?? '') ?>">
            <input type="text" name="user_name" placeholder="Nome do usuário" value="<?= htmlspecialchars($filters['user_name'] ?? '') ?>">
            <select name="status">
                <option value="">Status</option>
                <option value="pendente" <?= ($filters['status'] ?? '') === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="finalizado" <?= ($filters['status'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
            </select>
            <input type="date" name="date_start" value="<?= htmlspecialchars($filters['date_start'] ?? '') ?>">
            <input type="date" name="date_end" value="<?= htmlspecialchars($filters['date_end'] ?? '') ?>">
            <button type="submit">Filtrar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="6" class="empty">Nenhum serviço encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <?php $isFinished = !empty($service['finished_at']); ?>
                        <tr>
                            <td><?= (int) $service['id_service'] ?></td>
                            <td><?= htmlspecialchars($service['description']) ?></td>
                            <td><?= formatMoney((float) $service['price']) ?></td>
                            <td>
                                <span class="status <?= $isFinished ? 'finalizado' : 'pendente' ?>">
                                    <?= $isFinished ? 'Finalizado' : 'Pendente' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($service['user_name']) ?></td>
                            <td class="actions">
                                <button type="button">Alterar</button>
                                <button type="button" class="delete">Excluir</button>
                                <?php if (!$isFinished): ?>
                                    <button type="button">Finalizar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>

</html>