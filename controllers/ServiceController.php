<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Service.php';

/**
 * controller responsavel pelo cadastro de novos servicos.
 */
class ServiceController
{
    /**
     * garante que existe um usuario logado, redirecionando para o login
     * caso contrario. mesmo padrao usado no DashboardController.
     */
    private function requireLogin(): int
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        return (int) $_SESSION['user_id'];
    }

    /**
     * mostra o formulario de cadastro de novo servico (GET) ou processa
     * o envio (POST), conforme o metodo da requisicao.
     */
    public function create(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        require __DIR__ . '/../views/servico_cadastrar.php';
    }

    /**
     * valida e grava o novo servico. em caso de sucesso, redireciona pro
     * dashboard com mensagem de sucesso; em caso de falha, redireciona
     * de volta pro dashboard com mensagem de erro (conforme o README).
     */
    private function store(): void
    {
        $userId = $this->requireLogin();

        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';

        // normaliza valores tipo "1.234,56" ou "1234,56" para float
        $price = str_replace('.', '', $price);
        $price = str_replace(',', '.', $price);
        $price = (float) $price;

        if ($description === '' || $price <= 0) {
            header('Location: dashboard.php?erro=cadastro');
            exit;
        }

        Service::create($description, $price, $userId);

        header('Location: dashboard.php?sucesso=cadastro');
        exit;
    }
}
