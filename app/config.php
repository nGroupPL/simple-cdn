<?php

$config = [
    'hosts' => [

    ]
];


if (file_exists(__DIR__ . '/config-local.php')) {
    $config = array_merge_recursive($config, require_once __DIR__ . '/config-local.php');
}

return $config;