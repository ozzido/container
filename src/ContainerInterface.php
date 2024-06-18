<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Exception\CircularDependencyException;
use Ozzido\Container\Exception\ConstructException;
use Ozzido\Container\Exception\DependencyResolutionException;
use Ozzido\Container\Exception\MethodCallException;
use Ozzido\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Closure;

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
     * @throws CircularDependencyException
     * @throws ConstructException
     * @throws DependencyResolutionException
     * @throws MethodCallException
     * @throws NotFoundException
     */
    public function get(string $type);

    /**
     * Returns an array of instances for the requested tag.
     *
     * At the moment, this method completely ignores contextual binding tags, perhaps
     * this behavior will change in the future.
     *
     * @param non-empty-string $tag
     * @return list<mixed>
     *
     * @throws CircularDependencyException
     * @throws ConstructException
     * @throws DependencyResolutionException
     * @throws MethodCallException
     * @throws NotFoundException
     */
    public function getTagged(string $tag): array;

    /**
     * Sets an instance interceptor closure.
     *
     * @template T of object
     * @param class-string<T> $type
     * @param Closure(T, static): (T|void) $interceptor
     */
    public function interceptor(string $type, Closure $interceptor): void;

    /**
     * Resolves the dependencies of the given callable and calls it.
     *
     * @template T of object
     * @param array<non-empty-string, mixed> $arguments
     * @return ($callable is callable(): T ? T : mixed)
     *
     * @throws CircularDependencyException
     * @throws ConstructException
     * @throws DependencyResolutionException
     * @throws MethodCallException
     * @throws NotFoundException
     */
    public function call(callable $callable, array $arguments = [], bool $intercept = true): mixed;

    /**
     * Resolves the dependencies of the given concrete class and creates an instance of it.
     *
     * @template T of object
     * @param class-string<T> $concrete
     * @param array<non-empty-string, mixed> $arguments
     * @return T
     *
     * @throws CircularDependencyException
     * @throws ConstructException
     * @throws DependencyResolutionException
     * @throws MethodCallException
     */
    public function construct(string $concrete, array $arguments = [], bool $intercept = true): object;
}
