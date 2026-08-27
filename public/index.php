<?php

/**
 * ponto de entrada da aplicacao (front controller).
 * por enquanto so inicia a sessao e carrega a configuracao.
 * as rotas/controllers serao adicionados nos proximos commits.
 */

session_start();

require_once __DIR__ . '/../config/Database.php';

// roteamento simples sera adicionado aqui (login, dashboard, etc.)