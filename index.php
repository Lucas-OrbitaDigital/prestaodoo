<?php

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib/ripcord.php';
require_once __DIR__ . '/src/controllers/ProductController.php';
require_once __DIR__ . '/src/services/OdooService.php';

try {
    $odoo = new ProductService($config['odoo']);
    $controller = new ProductController($odoo);
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
