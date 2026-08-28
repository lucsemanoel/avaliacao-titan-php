<?php

session_start();

require_once __DIR__ . '/../controllers/ServiceController.php';

$controller = new ServiceController();
$controller->finalize();
