<?php

define('ROOT', dirname(__DIR__));
defined('DEV_MODE') || define('DEV_MODE', isset($_SERVER['DEV_MODE']) ? $_SERVER['DEV_MODE'] : false);

require_once ROOT . '/vendor/autoload.php';
require_once ROOT . '/app/Cdn.php';

try {
    (new \app\Cdn())->run(require_once ROOT . '/app/config.php');
} catch (Exception $e) {
    \app\Log::log($e->getCode() . ': ' . $e->getMessage());
//    switch ($e->getCode()) {
//        case 404:
//            $image = '404.png';
//            break;
//        default:
//            $image = '500.png';
//            break;
//    }

//    \app\Helper::output($image, 'png');
    \app\Helper::output(ROOT . '/dummy/other.png', 'png');
}
