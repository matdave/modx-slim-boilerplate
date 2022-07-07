<?php

namespace MODXSlim\Api\Controllers\Resources;

use MODX\Revolution\modResource;
use MODXSlim\Api\Exceptions\RestfulException;
use MODXSlim\Api\Traits\TemplateVariables;
use MODXSlim\Api\Transformers\ResourceTransformer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use MODXSlim\Api\Controllers\Restful;

class Search extends Restful
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
        $defaultParams = ['tvs' => null, 'query' => null, 'context' => 'web', 'page' => 1, 'limit' => 10];
        $paramsCast = ['ignoreMenu' => 'boolean'];
        $paramLimits = [
            'limit' => [
                'min' => 1,
                'max' => 10,
            ],
            'page' => [
                'min' => 1,
            ],
        ];
        $params = $this->getParams($request, $defaultParams, $paramsCast, $paramLimits);
        $tvs = explode(',', $params['tvs']);
        $condition = ['context_key' => $params['context'], 'published' => true, 'deleted' => false];
        if ($params['query']) {
            $query = [
                '`modResource`.`pagetitle`',
                '`modResource`.`longtitle`',
                '`modResource`.`description`',
                '`modResource`.`introtext`',
                '`modResource`.`content`',
            ];
            $condition[] = "MATCH(".implode(', ', $query).") AGAINST ('".$params['query']."')";
        }

        /** @var modResource $resource */

        $query = $this->modx->newQuery(modResource::class);
        $query->select($this->modx->getSelectColumns(modResource::class, 'modResource'));
        $query->where($condition);
        $total = $this->modx->getCount(modResource::class, $query);
        if($tvs) {
            $this->joinTVs($query, $tvs);
        }
        $query->limit($params['limit'], ($params['page'] - 1) * $params['limit']);
        $resources = $this->modx->getIterator(modResource::class, $query);
        if (!$resources) {
            throw RestfulException::notFound();
        }
        $data = [];
        foreach($resources as $resource) {
            $arr = $resource->toArray();
            $arr['content'] = $resource->parseContent();
            $data[] = $arr;
        }
        return $this->respondWithCollection($request, $data, ['total' => $total], $params);
    }
}
