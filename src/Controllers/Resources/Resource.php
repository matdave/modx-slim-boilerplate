<?php

namespace MODXSlim\Api\Controllers\Resources;

use MODX\Revolution\modResource;
use MODXSlim\Api\Exceptions\RestfulException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use MODXSlim\Api\Controllers\Restful;
use MODXSlim\Api\Traits\TemplateVariables;

class Resource extends Restful
{
    use TemplateVariables;
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
        $tvs = explode(',', $params['tvs']);
        $condition = ['id' => $request->getAttribute('id'), 'published' => true, 'deleted' => false];

        /** @var modResource $resource */

        $query = $this->modx->newQuery(modResource::class);
        $query->select($this->modx->getSelectColumns(modResource::class, 'modResource'));
        $query->where($condition);
        if($tvs) {
            $this->joinTVs($query, explode(',', $tvs));
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

