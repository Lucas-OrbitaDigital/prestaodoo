<?php

class Service
{
    private string $url;
    private string $db;
    private string $username;
    private string $password;
    private int $uid;

    public function __construct(array $config)
    {
        $this->url = rtrim($config['url'], '/');
        $this->db = $config['db'];
        $this->username = $config['username'];
        $this->password = $config['password'];

        $this->authenticate();
    }

    /**
     * Authenticate against Odoo using JSON-RPC.
     */
    private function authenticate(): void
    {
        $response = $this->jsonRpcCall(
            "{$this->url}/jsonrpc",
            'call',
            [
                'service' => 'common',
                'method'  => 'login',
                'args'    => [$this->db, $this->username, $this->password]
            ]
        );

        if (empty($response['result'])) {
            throw new Exception('Odoo authentication failed.');
        }

        $this->uid = $response['result'];
    }

    /**
     * Get data from Odoo by name filter using JSON-RPC.
     *
     * @param string $model
     * @param string $nameFilter
     * @param array $fields
     * 
     * @return array
     */
    public function getDataByName(string $model, string $nameFilter, array $fields): array
    {
        try {
            $response = $this->jsonRpcCall("{$this->url}/jsonrpc", 'call', [
                'service' => 'object',
                'method'  => 'execute_kw',
                'args'    => [$this->db, $this->uid, $this->password, $model, 'search_read', [[['name', 'ilike', $nameFilter]]], ['fields' => $fields]]
            ]);

            if (isset($response['error'])) {
                return [
                    'error' => true,
                    'message' => $response['error']['data']['message'] ?? 'Unknown Odoo error'
                ];
            }

            return $response['result'] ?? [];

        } catch (Throwable $e) {
            return [
                'error' => true,
                'message' => 'Odoo query failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Perform a JSON-RPC call to Odoo.
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * 
     * @return array
     */
    private function jsonRpcCall(string $url, string $method, array $params): array
    {
        $payload = [
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => $params,
            'id'      => uniqid(),
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 60,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);

        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);
        return json_decode($response, true);
    }
}
