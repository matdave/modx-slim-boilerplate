<?php

namespace MODXSlim\Api\Controllers\Resources;

use MODX\Revolution\modResource;
use MODXSlim\Api\Exceptions\RestfulException;
use MODXSlim\Api\Transformers\ResourceDetailTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use MODXSlim\Api\Controllers\Restful;

class Resource extends Restful
{
    protected static string $transformer = ResourceTransformer::class;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $defaultParams = ['tvs' => null];
        $params = $this->getParams($request, $defaultParams);
        $condition = ['id' => $request->getAttribute('id'), 'published' => true, 'deleted' => false];

        /** @var modResource $resource */

        $query = $this->modx->newQuery(modResource::class);
        $query->select($this->modx->getSelectColumns(modResource::class, 'modResource'));
        $query->where($condition);
        if($params['tvs']) {
            $this->joinTVs(explode(',', $params['tvs']));
        }
        $resource = $this->modx->getObject(modResource::class, $query);
        if (!$resource) {
            throw RestfulException::notFound();
        }

        return $this->respondWithItem($request, $resource);
    }
}

