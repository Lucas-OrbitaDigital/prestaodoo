<?php

class Service
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
        $common = Ripcord::client("{$this->url}/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        if (!$this->uid) {
            throw new Exception('Odoo authentication failed');
        }

        $this->models = Ripcord::client("{$this->url}/xmlrpc/2/object");
    }

    /**
     * Get data from Odoo by name filter.
     *
     * @param string $table
     * @param string $nameFilter
     * @param array $fields
     * @return array
     */
    public function getDataByName(string $table, string $nameFilter, array $fields): array
    {
        try {
            if (empty($table)) {
                throw new InvalidArgumentException('Table name cannot be empty.');
            }

            if (empty($nameFilter)) {
                throw new InvalidArgumentException('Name filter cannot be empty.');
            }

            if (empty($fields)) {
                throw new InvalidArgumentException('Fields array cannot be empty.');
            }

            $result = $this->models->execute_kw(
                $this->db,
                $this->uid,
                $this->password,
                "{$table}.template",
                'search_read',
                [
                    [['name', 'ilike', $nameFilter]]
                ],
                ['fields' => $fields]
            );

            if (!is_array($result)) {
                throw new RuntimeException("Unexpected response type from Odoo for table '{$table}'.");
            }

            return $result;

        } catch (Throwable $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}
