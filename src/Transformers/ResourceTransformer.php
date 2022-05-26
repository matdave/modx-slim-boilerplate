<?php

namespace MODXSlim\Api\Transformers;

use MODX\Revolution\modResource;

class ResourceTransformer extends TwigTransformer
{
    /**
     * Transform an item to an array structure appropriate for an API response.
     *
     * @param modResource $item  The item to transform.
     * @param  array  $transformerParams
     *
     * @return array
     * @throws \MODXSlim\Api\Exceptions\RestfulException
     */
    public function transform(modResource $item, array $transformerParams = []): array
    {
        $data = $item->toArray();
        $data['content'] = $item->parseContent();
        return $data;
    }
}
