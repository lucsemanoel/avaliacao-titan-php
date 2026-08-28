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

    /**
     * mostra o formulario de cadastro de novo usuario (GET).
     */
    public function showRegisterForm(): void
    {
        // se já está logado, nao faz sentido cadastrar outro usuario aqui
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard.php');
            exit;
        }

        require __DIR__ . '/../views/cadastro.php';
    }

    /**
     * valida e processa o cadastro de um novo usuario (POST).
     *
     * validacoes: campos obrigatorios preenchidos, e-mail em formato
     * valido, senha com tamanho minimo, e e-mail ainda nao cadastrado.
     * em caso de erro, mostra a mesma tela de cadastro com a mensagem.
     */
    public function register(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Preencha todos os campos.';
            require __DIR__ . '/../views/cadastro.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Informe um e-mail válido.';
            require __DIR__ . '/../views/cadastro.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = 'A senha deve ter pelo menos 6 caracteres.';
            require __DIR__ . '/../views/cadastro.php';
            return;
        }

        if (User::emailExists($email)) {
            $error = 'Já existe um usuário cadastrado com este e-mail.';
            require __DIR__ . '/../views/cadastro.php';
            return;
        }

        // como o wireframe só pede email e senha, o nome exibido no
        // dashboard ("Logado como: ...") é gerado a partir do email.
        // ex: jose.silva@email.com -> Jose Silva
        $localPart = strstr($email, '@', true) ?: $email;
        $name = ucwords(str_replace(['.', '_', '-'], ' ', $localPart));

        User::create($name, $email, $password);

        header('Location: login.php?sucesso=cadastro');
        exit;
    }
}
