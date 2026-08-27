<?php

require_once __DIR__ . '/../models/User.php';

/**
 * controller responsavel pelo processo de login e logout.
 */
class AuthController
{
    /**
     * mostra o formulario de login (usado quando acessa via GET).
     */
    public function showLoginForm(): void
    {
        // se já está logado, nao faz sentido ver o login de novo
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard.php');
            exit;
        }

        require __DIR__ . '/../views/login.php';
    }

    /**
     * processa a tentativa de login (usado quando o formulario eh enviado via POST).
     */
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);

        // password_verify compara a senha digitada com o hash salvo no banco
        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Ops, Email ou Senha inválido';
            require __DIR__ . '/../views/login.php';
            return;
        }

        // login OK: cria a sessão com os dados básicos do usuario
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];

        header('Location: dashboard.php');
        exit;
    }

    /**
     * encerra a sessão do usuário.
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
