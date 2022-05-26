<?php

namespace MODXSlim\Api\Transformers;

use xPDO\Om\xPDOObject;

class xPDOObjectTransformer extends Transformer
{
    /**
     * Transform an item to an array structure appropriate for an API response.
     *
     * @param  xPDOObject | array  $item  The item to transform.
     * @param  array  $transformerParams
     *
     * @return array
     */
    public function transform($item, array $transformerParams = []): array
    {
        if (is_array($item)) {
            return $item;
        }
        return $item->toArray();
    }
}
