<?php
namespace MODXSlim\Api\TypeCast;

class Type
{
    public static function int($value): int
    {
        return intval($value);
    }

    public static function float($value): float
    {
        return floatval($value);
    }

    public static function boolean($value): bool
    {
        return (($value === 1) || ($value === '1') || ($value === true) || ($value === 'true'));
    }
}
