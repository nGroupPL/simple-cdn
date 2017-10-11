<?php

$config = [
    'hosts' => [

    ]
];


if (file_exists('./config-local.php')) {
    $config = array_merge_recursive($config, require_once './config-local.php');
}

return $config;