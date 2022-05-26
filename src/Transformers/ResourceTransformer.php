<?php

namespace MODXSlim\Api\Transformers;

use MODX\Revolution\modResource;
use MODXSlim\Api\Exceptions\RestfulException;

class ResourceTransformer extends xPDOObjectTransformer
{
    /**
     * Transform an item to an array structure appropriate for an API response.
     *
     * @param modResource $item  The item to transform.
     * @param  array  $transformerParams
     *
     * @return array
     * @throws RestfulException
     */
    public function transform($item, array $transformerParams = []): array
    {
        $data = $item->toArray();
        $data['content'] = $item->parseContent();
        return $data;
    }
}
