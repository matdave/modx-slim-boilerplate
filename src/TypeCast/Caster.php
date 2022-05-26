<?php
namespace MODXSlim\Api\TypeCast;

class Caster
{
    /**
     * @param  array  $values
     * @param  array  $casts
     *
     * @throws \Exception
     */
    public static function castArray(array &$values, array $casts): void
    {
        foreach ($values as $key => $value) {
            if (!isset($casts[$key])) continue;
            if ($value === null) continue;

            $values[$key] = self::cast($value, $casts[$key]);
        }
    }

    /**
     * @param $value
     * @param $type
     *
     * @return mixed
     * @throws \Exception
     */
    public static function cast($value, $type): mixed
    {
        if (!method_exists(Type::class, $type)) {
            throw new \Exception("Cast {$type} not implemented.");
        }

        return Type::{$type}($value);
    }
}
