<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

define('APP_ENV', $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'DEVELOPMENT');
$settings = (require __DIR__ . '/settings.php')(APP_ENV);

define('MODX_API_MODE', true);
if (!defined('MODX_CORE_PATH')) {
    define('MODX_CORE_PATH', $settings['modx']['core_path']);
}
if (!defined('MODX_CONFIG_KEY')) {
    define('MODX_CONFIG_KEY', $settings['modx']['config_key'] ?? 'config');
}

require_once MODX_CORE_PATH . "vendor/autoload.php";

$settings = (require __DIR__ . '/dependencies.php')($settings);

$container = new MODXSlim\Api\DI\Container($settings);

return $container;
