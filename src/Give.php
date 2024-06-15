<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Closure;

/**
 * @template T of object
 */
final readonly class Give
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
     */
    public function resolve(ContainerInterface $container): mixed
    {
        if ($this->typeOrFactory instanceof Closure) {
            return ($this->typeOrFactory)($container);
        }

        return $container->get($this->typeOrFactory);
    }
}
