<?php

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/controllers/ProductController.php';
require_once __DIR__ . '/src/services/OdooService.php';
require_once __DIR__ . '/src/common.php';

try {
    $service = new Service($config['odoo']);
    $controller = new ProductController($service);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

foreach (glob(__DIR__ . '/src/routes/*.php') as $routeFile) {
    require $routeFile;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
