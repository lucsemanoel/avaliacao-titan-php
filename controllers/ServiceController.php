<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/User.php';

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

    /**
     * finaliza o servico informado via POST: grava a data de finalizacao,
     * calcula a comissao (feito dentro do model) e envia um e-mail pro
     * usuario responsavel pelo servico. so aceita POST.
     */
    public function finalize(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: dashboard.php');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Location: dashboard.php');
            exit;
        }

        $service = Service::finalize($id);

        // Service::finalize() retorna null se o id nao existir ou se o
        // servico ja estava finalizado (ver comentario no model)
        if (!$service) {
            header('Location: dashboard.php?erro=finalizacao');
            exit;
        }

        $user = User::findById((int) $service['user_id_user']);

        if ($user) {
            $this->sendFinalizationEmail($user, $service);
        }

        header('Location: dashboard.php?sucesso=finalizacao');
        exit;
    }

    /**
     * envia (ou registra, se o servidor de e-mail local nao estiver
     * configurado) o e-mail avisando que o servico foi finalizado e
     * informando o valor da comissao calculada.
     *
     * em ambiente XAMPP local, mail() geralmente falha por falta de
     * SMTP configurado - por isso, se mail() retornar false, gravamos
     * o conteudo do e-mail em storage/emails.log como fallback, so pra
     * ser possivel validar o fluxo sem precisar configurar um SMTP real.
     */
    private function sendFinalizationEmail(array $user, array $service): void
    {
        $subject = 'Serviço finalizado - #' . $service['id_service'];

        $body = "Olá, {$user['name']}!\n\n"
            . "O serviço \"{$service['description']}\" foi finalizado.\n"
            . 'Valor do serviço: R$ ' . number_format((float) $service['price'], 2, ',', '.') . "\n"
            . 'Comissão: R$ ' . number_format((float) $service['commission_user'], 2, ',', '.') . "\n";

        $headers = 'From: nao-responda@jminformatica.com';

        $sent = @mail($user['email'], $subject, $body, $headers);

        if (!$sent) {
            $logDir = __DIR__ . '/../storage';

            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }

            $log = '[' . date('Y-m-d H:i:s') . "] Para: {$user['email']}\n"
                . "Assunto: {$subject}\n{$body}\n"
                . str_repeat('-', 40) . "\n";

            file_put_contents($logDir . '/emails.log', $log, FILE_APPEND);
        }
    }

    /**
     * retorna os servicos filtrados em JSON, usado pelo fetch() do dashboard
     * (dashboard.js) para atualizar a tabela sem recarregar a pagina.
     */
    public function filterJson(): void
    {
        $this->requireLogin();

        $filters = [
            'date_start'  => $_GET['date_start'] ?? '',
            'date_end'    => $_GET['date_end'] ?? '',
            'description' => $_GET['description'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'user_name'   => $_GET['user_name'] ?? '',
        ];

        $services = Service::findAll($filters);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($services);
    }
}
