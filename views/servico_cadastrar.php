<?php

/**
 * view do formulario de cadastro de novo servico.
 * chamada pelo ServiceController::create() quando o metodo eh GET.
 */
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Serviço - Ordem de Serviços</title>
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
            margin: 0 0 24px;
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

        a.voltar {
            display: inline-block;
            margin-top: 16px;
            color: #2563eb;
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>Cadastrar Novo Serviço</h1>

        <form method="POST" action="servico_cadastrar.php">
            <input type="text" name="description" placeholder="descrição" required>
            <input type="text" name="price" placeholder="preço" required>
            <button type="submit">Cadastrar</button>
        </form>

        <a class="voltar" href="dashboard.php">&larr; Voltar ao dashboard</a>
    </div>
</body>

</html>