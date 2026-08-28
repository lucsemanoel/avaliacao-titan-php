<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário - Ordem de Serviços</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-box {
            width: 100%;
            max-width: 550px;
            padding: 0 24px;
        }

        .register-box h1 {
            font-size: 32px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 32px;
        }

        .register-box input {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 20px;
            border: 2px solid #1a1a1a;
            border-radius: 2px;
            font-size: 18px;
            color: #1a1a1a;
        }

        .register-box input::placeholder {
            color: #aaa;
        }

        .register-box input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .actions button {
            padding: 14px 40px;
            background: #1a1a1a;
            color: #fff;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            font-size: 16px;
        }

        .actions button:hover {
            background: #000;
        }

        .actions a {
            color: #2563eb;
            text-decoration: underline;
            font-size: 16px;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="register-box">
        <h1>Cadastrar Novo Usuário</h1>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="cadastro.php">
            <input type="email" id="email" name="email" placeholder="email@email.com"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <input type="password" id="password" name="password" placeholder="**************" required>

            <div class="actions">
                <button type="submit">Cadastrar</button>
                <a href="login.php">Já tenho uma conta</a>
            </div>
        </form>
    </div>
</body>

</html>