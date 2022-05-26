<?php

declare(strict_types=1);

use Middlewares\TrailingSlash;
use Psr\Log\LoggerInterface;
use Slim\App;
use MODXSlim\Api\Middleware\ErrorHandler;

return static function (App $app, array $settings) {
    $app->addRoutingMiddleware();

    $app->add(new TrailingSlash(false));
    $app->addBodyParsingMiddleware();

    $errorHandler = new ErrorHandler(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $app->getContainer()->get(LoggerInterface::class)
    );

    $errorMiddleware = $app->addErrorMiddleware(false, false, false);
    $errorMiddleware->setDefaultErrorHandler($errorHandler);
    $errorHandler->forceContentType('application/json');

    $corsEnabled = $settings['cors']['enabled'] ?? false;
    if ($corsEnabled) {
        $origin = $settings['cors']['origin'] ?? '*';
        $app->add(
            function ($request, $handler) use ($origin) {
                $response = $handler->handle($request);
                return $response
                    ->withHeader('Access-Control-Allow-Origin', $origin)
                    ->withHeader(
                        'Access-Control-Allow-Headers',
                        'X-Requested-With, Content-Type, Accept, Origin, Authorization'
                    )
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
            }
        );
    }
};
