<?php

/**
 * view do formulario de edicao de servico.
 * chamada pelo ServiceController::edit() quando o metodo eh GET.
 * espera receber $service (array com id_service, description, price, ...).
 */
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Serviço - Ordem de Serviços</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            color: #1a1a1a;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 80px;
        }

        .box {
            width: 420px;
        }

        h1 {
            font-size: 26px;
            margin: 0 0 8px;
        }

        p.info {
            margin: 0 0 20px;
            color: #666;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 16px;
            border: 2px solid #1a1a1a;
            border-radius: 2px;
            font-size: 16px;
        }

        button {
            padding: 12px 24px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 2px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #333;
        }

        p.erro {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        a.voltar {
            display: inline-block;
            margin-top: 16px;
            color: #2563eb;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>Editar Serviço</h1>
        <p class="info">ID #<?= (int) $service['id_service'] ?></p>

        <?php if (($_GET['erro'] ?? '') === 'edicao'): ?>
            <p class="erro">Não foi possível salvar. Verifique os dados informados.</p>
        <?php endif; ?>

        <form method="POST" action="servico_editar.php">
            <input type="hidden" name="id" value="<?= (int) $service['id_service'] ?>">
            <input type="text" name="description" placeholder="descrição"
                value="<?= htmlspecialchars($service['description']) ?>" required>
            <input type="text" name="price" placeholder="preço"
                value="<?= htmlspecialchars(number_format((float) $service['price'], 2, ',', '')) ?>" required>
            <button type="submit">Salvar</button>
        </form>

        <a class="voltar" href="dashboard.php">&larr; Voltar ao dashboard</a>
    </div>
</body>

</html>