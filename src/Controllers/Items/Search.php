<?php

namespace MODXSlim\Api\Controllers\Items;

use BasePackage\Model\Item;
use MODXSlim\Api\Exceptions\RestfulException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use MODXSlim\Api\Controllers\Restful;

class Search extends Restful
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $defaultParams = ['query' => null, 'page' => 1, 'limit' => 10];
        $paramsCast = ['featured' => 'boolean'];
        $paramLimits = [
            'limit' => [
                'min' => 1,
                'max' => 10,
            ],
            'page' => [
                'min' => 1,
            ],
        ];
        $condition = [];
        $params = $this->getParams($request, $defaultParams, $paramsCast, $paramLimits);
        if ($params['query']) {
            $query = [
                '`Item`.`title`',
                '`Item`.`description`',
            ];
            $condition[] = "MATCH(".implode(', ', $query).") AGAINST ('".$params['query']."' WITH QUERY EXPANSION)";
        }
        if ($params['featured']) {
            $condition['featured:='] = $params['featured'];
        }

        /** @var Item $item */

        $query = $this->modx->newQuery(Item::class);
        $query->select($this->modx->getSelectColumns(Item::class, 'Item'));
        $query->where($condition);
        $total = $this->modx->getCount(Item::class, $query);
        $query->limit($params['limit'], ($params['page'] - 1) * $params['limit']);
        $items = $this->modx->getIterator(Item::class, $query);
        if (!$items) {
            throw RestfulException::notFound();
        }
        $data = [];
        foreach($items as $item) {
            $data[] = $item->toArray();
        }
        return $this->respondWithCollection($request, $data, ['total' => $total], $params);
    }
}
