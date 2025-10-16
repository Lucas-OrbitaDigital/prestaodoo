<?php

require_once __DIR__ . '/../services/Service.php';

class ProductController
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * List all products matching a filter ('atmoss') and return as JSON.
     * @param string $model
     * @param string $nameFilter
     * @param array $fields
     * 
     */
    public function getAllByName(string $model, string $nameFilter, array $fields): void
    {
        $products = $this->service->getDataByName($model, $nameFilter, $fields);

        if (!empty($products['error'])) {
            jsonResponse(['error' => 'Odoo query failed', 'details' => $products['message']], 500);
        }

        jsonResponse($products);
    }
}
