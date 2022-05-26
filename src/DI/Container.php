<?php

namespace MODXSlim\Api\DI;

use DMODXSlim\Api\DI\Exception\ContainerException;
use DMODXSlim\Api\DI\Exception\NotFoundException;
use DMODXSlim\Api\DI\Interfaces\ResolverClassInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{

    /**
     * @var array
     */
    private $definitions = [];

    /**
     * @var array
     */
    private $resolvedEntries = [];

    /**
     * @var ResolverClassInterface|null
     */
    private $resolver;

    public function __construct(array $definitions, ?ResolverClassInterface $resolver = null)
    {
        $this->definitions = array_merge($definitions, [ContainerInterface::class => $this]);
        $this->resolver = $resolver;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get(string $id): mixed
    {
        if ($this->has($id) === false) {
            throw new NotFoundException("No entry or class found for '$id'");
        }

        if (array_key_exists($id, $this->resolvedEntries)) {
            return $this->resolvedEntries[$id];
        } elseif (array_key_exists($id, $this->definitions)) {
            $value = $this->definitions[$id];
            if ($value instanceof \Closure) {
                $value = $value($this);
            }
        } else {
            $value = $this->resolve($id);
        }

        $this->resolvedEntries[$id] = $value;
        return $value;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        if (array_key_exists($id, $this->definitions) || array_key_exists($id, $this->resolvedEntries)) {
            return true;
        }

        return class_exists($id) && $this->resolver instanceof ResolverClassInterface;
    }

    /**
     * @param string $class
     * @return object
     * @throws ContainerException
     */
    private function resolve(string $class): object
    {
        if ($this->resolver instanceof ResolverClassInterface) {
            try {
                return $this->resolver->resolve($class, $this);
            } catch (\Exception $e) {
                throw new ContainerException(sprintf('Cannot resolve entry "%s" : %s', $class, $e->getMessage()));
            }
        }

        throw new ContainerException("Resolver is disabled or missing");
    }
}
