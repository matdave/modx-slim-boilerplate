<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use MODXSlim\Api\Configuration;
use MODX\Revolution\modX;

return static function (array $settings) {
    return [
        'settings' => $settings,

        Configuration::class => function (ContainerInterface $c) {
            return new Configuration($c->get('settings'));
        },

        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        modX::class => function () {
            $modx = new modX();
            $modx->initialize('web');
            return $modx;
        },

        \Psr\Http\Client\ClientInterface::class => function (ContainerInterface $c) {
            $modx = $c->get(modX::class);
            return $modx->services->get(\Psr\Http\Client\ClientInterface::class);
        },

        \Psr\Http\Message\RequestFactoryInterface::class => function (ContainerInterface $c) {
            $modx = $c->get(modX::class);
            return $modx->services->get(\Psr\Http\Message\RequestFactoryInterface::class);
        }
    ];
};
