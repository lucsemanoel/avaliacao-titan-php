<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Service.php';

/**
 * controller responsavel pelo cadastro, edicao e exclusao de servicos.
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

    /**
     * mostra o formulario de edicao (GET) ou processa a alteracao (POST)
     * do servico informado via ?id=.
     */
    public function edit(): void
    {
        $this->requireLogin();

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Location: dashboard.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        $service = Service::findById($id);

        if (!$service) {
            header('Location: dashboard.php');
            exit;
        }

        require __DIR__ . '/../views/servico_editar.php';
    }

    /**
     * valida e grava a alteracao do servico. mesmas regras de validacao
     * do cadastro (descricao obrigatoria, preco > 0).
     */
    private function update(int $id): void
    {
        $this->requireLogin();

        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';

        $price = str_replace('.', '', $price);
        $price = str_replace(',', '.', $price);
        $price = (float) $price;

        if ($description === '' || $price <= 0) {
            header("Location: servico_editar.php?id={$id}&erro=edicao");
            exit;
        }

        Service::update($id, $description, $price);

        header('Location: dashboard.php?sucesso=edicao');
        exit;
    }

    /**
     * exclui o servico informado via ?id= e volta pro dashboard.
     * so aceita POST, para evitar exclusao acidental via link direto.
     */
    public function delete(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: dashboard.php');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            Service::delete($id);
        }

        header('Location: dashboard.php?sucesso=exclusao');
        exit;
    }
}
