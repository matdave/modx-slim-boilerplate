<?php

namespace MODXSlim\Api\Controllers;

use MODX\Revolution\modX;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use MODXSlim\Api\Exceptions\RestfulException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use MODXSlim\Api\Transformers\Transformer;
use MODXSlim\Api\Transformers\xPDOObjectTransformer;
use MODXSlim\Api\TypeCast\Caster;
use Middlewares\Utils\FactoryInterface;

abstract class Restful implements RequestHandlerInterface
{
    /** @var FactoryInterface */
    private FactoryInterface $factoryInterface;

    protected static string $transformer = xPDOObjectTransformer::class;

    /** @var modX */
    protected modX $modx;

    public function __construct(modX $modx, FactoryInterface $factoryInterface)
    {
        $this->factoryInterface = $factoryInterface;
        $this->modx = $modx;
    }

    /**
     * @throws RestfulException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        }

        throw RestfulException::notImplemented();
    }

    /**
     * Merge url params and query params
     *
     * @param ServerRequestInterface $request
     * @param array $defaultParams
     * @param array $paramsCast
     * @param array $paramLimits
     * @return array
     * @throws RestfulException
     */
    protected function getParams(ServerRequestInterface $request, array $defaultParams = [], array $paramsCast = [], array $paramLimits = []): array
    {
        $urlParams = $request->getAttribute('params', '');

        $parsedParams = [];

        if ($urlParams !== null) {
            $params = explode('/', $urlParams);
            foreach ($params as $param) {
                [$key, $value] = explode(':', $param);
                if ($value !== null) {
                    $parsedParams[$key] = $value;
                }
            }
        }

        $allParams = array_merge($defaultParams, $request->getQueryParams(), $parsedParams);

        try {
            Caster::castArray($allParams, $paramsCast);
        } catch (\Exception $e) {
            throw RestfulException::internalServerError(['message' => $e->getMessage()]);
        }

        $checkParamLimits = $request->getAttribute('checkParamLimits', true);
        if ($checkParamLimits && !empty($paramLimits)) {
            foreach ($paramLimits as $key => $limits) {
                if (!isset($allParams[$key])) {
                    continue;
                }

                foreach ($limits as $name => $value) {
                    switch ($name) {
                        case 'min':
                            if ($allParams[$key] < $value) {
                                throw RestfulException::badRequest(['query' => $key]);
                            }
                            break;
                        case 'max':
                            if ($allParams[$key] > $value) {
                                throw RestfulException::badRequest(['query' => $key]);
                            }
                            break;
                    }
                }
            }
        }

        return $allParams;
    }

    /**
     * @param ServerRequestInterface $request
     * @param \Iterator|array $collection
     * @param null $transformer
     * @param array $transformerParams
     * @param array $meta
     * @param array $params
     * @return ResponseInterface
     */
    protected function respondWithCollection(ServerRequestInterface $request, \Iterator|array $collection, $transformer = null, array $transformerParams = [], array $meta = [], array $params = []): ResponseInterface
    {
        $transformer = $this->factoryInterface->make($transformer ?: static::$transformer);
        $data = $transformer->transformCollection($collection, $transformerParams);

        $total = $meta['total'] ?? count($data);
        $returned = count($data);
        $page = ($params['page']) ? (int)$params['page'] : 1;
        $limit = (int)$params['limit'];

        $hasMore = false;
        if ($limit !== 0) {
            $hasMore = (($page - 1) * $limit + $returned) < $total;
        }

        return $this->respond($request, [
            'total' => (int)$total,
            'hasMore' => $hasMore,
            'returned' => (int)$returned,
            'params' => $params,
            'data' => $data
        ]);
    }

    protected function respondWithItem(ServerRequestInterface $request, $item, $transformer = null, array $transformerParams = []): ResponseInterface
    {
        $transformer = $this->factoryInterface->make($transformer ?: static::$transformer);

        return $this->respond($request, $transformer->transformItem($item, $transformerParams));
    }

    protected function respond(ServerRequestInterface $request, array $data): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}
