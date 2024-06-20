<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Exception\CircularDependencyException;
use Ozzido\Container\Exception\ConstructException;
use Ozzido\Container\Exception\DependencyResolutionException;
use Ozzido\Container\Exception\MethodCallException;
use Ozzido\Container\Exception\NotFoundException;
use Closure;

/**
 * @template T of object
 */
final readonly class Lazy
{
    /**
     * @param non-empty-string|class-string<T>|Closure(ContainerInterface): T $typeOrFactory
     */
    public function __construct(private string|Closure $typeOrFactory)
    {
    }

    /**
     * Resolves a dependency by retrieving it from a container or calling a factory.
     *
     * @return T|mixed
     *
     * @throws CircularDependencyException
     * @throws ConstructException
     * @throws DependencyResolutionException
     * @throws MethodCallException
     * @throws NotFoundException
     */
    public function resolve(ContainerInterface $container): mixed
    {
        if ($this->typeOrFactory instanceof Closure) {
            return ($this->typeOrFactory)($container);
        }

        return $container->get($this->typeOrFactory);
    }
}
