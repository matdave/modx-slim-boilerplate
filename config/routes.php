<?php
declare(strict_types=1);

use MODXSlim\Api\Controllers\Resources\Children;
use MODXSlim\Api\Controllers\Resources\List;
use MODXSlim\Api\Controllers\Resources\Resource;
use MODXSlim\Api\Controllers\Resources\Search;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use MODXSlim\Api\Middleware\Restful;

return new class {
    const PARAMS = '{params:.*}';
    const ID = '{id:[0-9]+}';
    const ALIAS = '{alias:[a-zA-Z\-_0-9]+}';

    public function __invoke(App $app)
    {
        /** @var Restful $restful */
        $restful = $app->getContainer()->get(Restful::class);
        $app->group(
            '/resources',
            function (RouteCollectorProxy $group) use ($restful) {
                $group->any('/list[/' . self::PARAMS . ']', Listing::class)->add(
                    $restful->withAllowedMethods(['GET'])
                );
                $group->any('/search[/' . self::PARAMS . ']', Search::class)->add(
                    $restful->withAllowedMethods(['GET'])
                );
                $group->group(
                    '/' . self::ID,
                    function (RouteCollectorProxy $group) use ($restful) {
                        $group->any('', Resource::class)->add(
                            $restful->withAllowedMethods(['GET'])
                        );
                        $group->any('/children[/' . self::PARAMS . ']', Children::class)->add(
                            $restful->withAllowedMethods(['GET'])
                        );
                    }
                );
            }
        );
    }
};
