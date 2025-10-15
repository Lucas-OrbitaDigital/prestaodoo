<?php

require_once __DIR__ . '/../services/ProductService.php';

class ProductController
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * List all products matching a filter ('atmoss') and return as JSON.
     *
     * @return void
     */
    public function getAllByName(): void
    {
        $products = $this->service->getDataByName('product', 'atmoss', ['id', 'name', 'qty_available']);

        if (!empty($products['error'])) {
            jsonResponse(['error' => 'Odoo query failed', 'details' => $products['message']], 500);
        }

        jsonResponse($products);
    }
}
