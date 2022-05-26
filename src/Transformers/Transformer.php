<?php

namespace MODXSlim\Api\Transformers;


use MODXSlim\Api\Configuration;

abstract class Transformer
{
    /** @var array */
    protected $excludes = [];

    /**
     * Create a transformer and use it to transform an item.
     *
     * @param  mixed  $item
     * @param  array  $transformerParams
     *
     * @return array
     */
    public function transformItem($item, array $transformerParams = []): array
    {
        return $this->transform($item, $transformerParams);
    }

    /**
     * Create a transformer and use it to transform a collection of items.
     *
     * @param  array|\Iterator  $collection
     * @param  array  $transformerParams
     *
     * @return array
     */
    public function transformCollection($collection, array $transformerParams = []): array
    {
        $transformed = [];
        foreach ($collection as $key => $item) {
            $transformed[$key] = $this->transform($item, $transformerParams);
        }

        return $transformed;
    }

    /**
     * Transform an item to an array structure appropriate for an API response.
     *
     * @param  mixed  $item  The item to transform.
     * @param  array  $transformerParams
     *
     * @return array
     */
    abstract public function transform($item, array $transformerParams = []): array;

    protected function mask(array $fields, array $excludes = [])
    {
        return array_diff($fields, $this->excludes($excludes));
    }

    protected function excludes(array $excludes): array
    {
        return array_merge($this->excludes, $excludes);
    }
}
