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
    public function list(): void
    {
        $products = $this->service->getDataByName('atmoss', ['id', 'name', 'list_price']);
        jsonResponse($products);
    }
}
