<?php
namespace MODXSlim\Api\Traits;

use MODX\Revolution\modTemplateVar;
use MODX\Revolution\modTemplateVarResource;
use MODXSlim\Api\Exceptions\RestfulException;
use xPDO\Om\xPDOQuery;

trait TemplateVariables
{

    private static $validCast = ['DATE', 'DATETIME', 'DECIMAL', 'TIME', 'CHAR', 'NCHAR', 'SIGNED', 'UNSIGNED', 'BINARY'];

    /**
     * @param xPDOQuery $query
     * @param string $name
     * @param null|string  $cast
     *
     * @throws RestfulException
     */
    protected function joinTV(xPDOQuery &$query, string $name, string $cast = null): void
    {
        $tv = $this->modx->getObject(modTemplateVar::class, ['name' => $name]);
        if (empty($tv)) {
            return;
        }
        if ($cast !== null) {
            $castType = explode('(', $cast);
            $castType = strtoupper($castType[0]);

            if (!in_array($castType, self::$validCast)) {
                throw RestfulException::internalServerError(['message' => "invalid cast for {$name}"]);
            }
        }

        $alias = $name . '_tvr';

        $query->leftJoin(modTemplateVarResource::class, $alias, ["{$alias}.contentid = modResource.id", "{$alias}.tmplvarid = {$tv->id}"]);
        $query->select(
            [
                $name => $cast ? "CAST({$alias}.value AS {$cast})" : "{$alias}.value"
            ]
        );
    }

    /**
     * @param xPDOQuery $query
     * @param array  $names
     * @param array  $cast
     *
     * @throws RestfulException
     */
    protected function joinTVs(xPDOQuery &$query, array $names, array $cast = []): void
    {
        foreach ($names as $name) {
            $this->joinTV($query, $name, $cast[$name] ?? null);
        }
    }
}
