<?php

declare(strict_types=1);

use Monolog\Logger;

return static function (string $appEnv) {
    $settings = [
        'app_env' => $appEnv,
        'display_error_details' => false,
        'log_errors' => true,
        'base_path_app' => '',
        'base_path_manage' => '',
        'logger' => [
            'name' => 'api',
            'path' => dirname(dirname(__FILE__)) . '/var/log/app.log',
            'level' => Logger::ERROR
        ],
    ];

    if ($appEnv === 'DEVELOPMENT' || $appEnv === 'TEST') {
        $settings['display_error_details'] = true;
        $settings['logger']['level'] = Logger::DEBUG;
    }

    if (is_readable(__DIR__ . '/local.php')) {
        $localSettings = require __DIR__ . '/local.php';
        $settings = array_merge($settings, $localSettings);
    }

    return $settings;
};
