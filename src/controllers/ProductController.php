<?php

require_once __DIR__ . '/../services/ProductService.php';

class ProductController
{
    private $odoo;

    public function __construct(ProductService $odoo)
    {
        $this->odoo = $odoo;
    }

    /**
     * Send a JSON response with HTTP status code.
     *
     * @param mixed $data
     * @param int $status
     * @return void
     */
    private function jsonResponse($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * List all products matching a filter ('atmoss') and return as JSON.
     *
     * @return void
     */
    public function list(): void
    {
        $products = $this->odoo->getProductsByName('atmoss', ['id', 'name', 'list_price']);
        $this->jsonResponse($products);
    }
}
