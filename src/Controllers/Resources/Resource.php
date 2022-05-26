<?php

namespace MODXSlim\Api\Controllers\Resources;

use MODX\Revolution\modResource;
use MODXSlim\Api\Exceptions\RestfulException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use MODXSlim\Api\Controllers\Restful;

class Resource extends Restful
{
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
        $data = $resource->toArray();
        $data['content'] = $resource->parseContent();
        return $this->respondWithItem($request, $data);
    }
}

