<?php

namespace MODXSlim\Api\DI\Interfaces;

use Psr\Container\ContainerInterface;

interface ResolverClassInterface
{
    /**
     * @param string $class
     * @param ContainerInterface $container
     * @return object
     * @throws \Exception if can't resolve class
     */
    public function resolve(string $class, ContainerInterface $container): object;
}
