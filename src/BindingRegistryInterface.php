<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Binding\BindingInterface;

interface BindingRegistryInterface
{
    /**
     * Registers a new binding type.
     *
     * @param non-empty-string|class-string $type
     */
    public function bind(string $type): BindingRegistrar;

    /**
     * Checks whether a binding for the given type exists in the registry.
     *
     * @param non-empty-string|class-string $type
     */
    public function bound(string $type): bool;

    /**
     * Removes a binding of the given type from the registry.
     *
     * @param non-empty-string|class-string $type
     */
    public function unbind(string $type): void;

    /**
     * Adds a binding of the given type to the registry.
     *
     * @param non-empty-string|class-string $type
     */
    public function addBinding(string $type, BindingInterface $binding): void;

    /**
     * Returns a binding of the given type if it's exists in the registry, otherwise returns null.
     *
     * @param non-empty-string|class-string $type
     */
    public function getBinding(string $type): ?BindingInterface;

    /**
     * Returns a list of all bindings from the registry.
     *
     * @return array<non-empty-string|class-string, BindingInterface>
     */
    public function getBindings(): array;

    /**
     * Resets resolved instances for all bindings in the registry that have a scoped lifecycle.
     */
    public function resetScoped(): void;
}
