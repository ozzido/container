<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Exception\ContainerException;
use Ozzido\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends BindingRegistryInterface, PsrContainerInterface
{
    /**
     * Creates contextual binding registry for the given concrete class.
     *
     * @param class-string $concrete
     */
    public function for(string $concrete): BindingRegistryInterface;

    /**
     * Checks if container is able to provide instance of the requested type.
     *
     * @param non-empty-string|class-string $type
     */
    public function has(string $type): bool;

    /**
     * Returns an instance for the requested type.
     *
     * @template T of object
     * @param non-empty-string|class-string $type
     * @return ($type is class-string<T> ? T : mixed)
     *
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(string $type);

    /**
     * Resolves the dependencies of the given callable and calls it.
     *
     * @template T of object
     * @param array<non-empty-string, mixed> $arguments
     * @return ($callable is callable(): T ? T : mixed)
     *
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function call(callable $callable, array $arguments = []): mixed;

    /**
     * Resolves the dependencies of the given concrete class and creates an instance of it.
     *
     * @template T of object
     * @param class-string<T> $concrete
     * @param array<non-empty-string, mixed> $arguments
     * @return T
     *
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function construct(string $concrete, array $arguments = []): object;
}
