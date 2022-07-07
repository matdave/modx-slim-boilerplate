<?php

namespace MODXSlim\Api\Controllers\Items;

use BasePackage\Model\Item as BPItem;
use MODXSlim\Api\Exceptions\RestfulException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use MODXSlim\Api\Controllers\Restful;

class Item extends Restful
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $condition = ['id' => $request->getAttribute('id')];

        /** @var Item $item */

        $query = $this->modx->newQuery(BPItem::class);
        $query->select($this->modx->getSelectColumns(BPItem::class, 'Item'));
        $query->where($condition);
        $item = $this->modx->getObject(BPItem::class, $query);
        if (!$item) {
            throw RestfulException::notFound();
        }
        $data = $item->toArray();
        return $this->respondWithItem($request, $data);
    }
}
