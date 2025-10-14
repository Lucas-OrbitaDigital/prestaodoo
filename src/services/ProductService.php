<?php

require_once __DIR__ . '/../../lib/ripcord.php';

class ProductService
{
    private $url;
    private $db;
    private $username;
    private $password;
    private $uid;
    private $models;

    public function __construct(array $config)
    {
        $this->url = $config['url'];
        $this->db = $config['db'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        $this->init();
    }

    private function init(): void
    {
        $common = ripcord::client("{$this->url}/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        if (!$this->uid) {
            throw new Exception('Odoo authentication failed');
        }

        $this->models = ripcord::client("{$this->url}/xmlrpc/2/object");
    }

    /**
     * Get products from Odoo by name filter.
     *
     * @param string $nameFilter
     * @param array $fields
     * @return array
     */
    public function getProductsByName(string $nameFilter, array $fields = ['id', 'name', 'list_price']): array
    {
        return $this->models->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'product.template',
            'search_read',
            [
                [['name', 'ilike', $nameFilter]]
            ],
            ['fields' => $fields]
        );
    }
}
