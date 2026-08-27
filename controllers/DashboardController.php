<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Service.php';

/**
 * controller responsavel pela tela principal (dashboard).
 */
class DashboardController
{
    public function index(): void
    {
        // guarda de sessao: sem usuario logado, nao ha dashboard
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $loggedUser = User::findById($_SESSION['user_id']);

        // se o usuario da sessao nao existe mais (ex.: foi desativado), desloga
        if (!$loggedUser) {
            session_destroy();
            header('Location: login.php');
            exit;
        }

        // filtros vindos da URL (implementados por completo em um proximo commit)
        $filters = [
            'date_start'  => $_GET['date_start'] ?? '',
            'date_end'    => $_GET['date_end'] ?? '',
            'description' => $_GET['description'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'user_name'   => $_GET['user_name'] ?? '',
        ];

        $services = Service::findAll($filters);
        $totalValue = Service::totalValueByUser($loggedUser['id_user']);
        $pendingServices = Service::pendingByUser($loggedUser['id_user'], 5);
        $today = date('d/m/Y');

        require __DIR__ . '/../views/dashboard.php';
    }
}
