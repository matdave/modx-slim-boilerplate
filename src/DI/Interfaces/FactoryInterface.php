<?php

namespace MODXSlim\Api\DI\Interfaces;

use InvalidArgumentException;
use MODXSlim\Api\DI\Exception\DependencyException;
use MODXSlim\Api\DI\Exception\NotFoundException;

interface FactoryInterface
{
    /**
     * Resolves an entry by its name. If given a class name, it will return a new instance of that class.
     *
     * @param string $name       Entry name or a class name.
     * @param array  $parameters Optional parameters to use to build the entry. Use this to force specific
     *                           parameters to specific values. Parameters not defined in this array will
     *                           be automatically resolved.
     *
     * @return mixed
     *@throws DependencyException       Error while resolving the entry.
     * @throws NotFoundException         No entry or class found for the given name.
     * @throws InvalidArgumentException The name parameter must be of type string.
     */
    public function make(string $name, array $parameters = []): mixed;
}
