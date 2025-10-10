<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/products' && $method === 'GET') {
    $controller->list();
    exit;
}
