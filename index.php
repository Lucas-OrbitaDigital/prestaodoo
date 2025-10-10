<?php

require_once __DIR__ . '/lib/ripcord.php';

$url = "https://x@odoo.com";
$db = "odoo_db";
$username = "admin";
$password = "password";

$common = ripcord::client("$url/xmlrpc/2/common");

$uid = $common->authenticate($db, $username, $password, []);

if (!$uid) {
    echo "Autentication failed";
}

$models = ripcord::client("$url/xmlrpc/2/object");
$atmossProducts = $models->execute_kw(
    $db,
    $uid,
    $password,
    'product.product',
    'search_read',
    [
        [
            ['name', 'ilike', 'atmoss']
        ]
    ],
    [
        'fields' => ['id', 'name', 'price']
    ]
);
