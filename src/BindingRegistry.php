<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Binding\Capability\HasLifecycleInterface;
use Ozzido\Container\Binding\BindingInterface;
use Ozzido\Container\Binding\Lifecycle;

class BindingRegistry implements BindingRegistryInterface
{
    /** @var array<non-empty-string|class-string, BindingInterface> */
    private array $bindings = [];

    /** @inheritdoc */
    public function bind(string $type): BindingRegistrar
    {
        return new BindingRegistrar($type, $this);
    }

    /** @inheritdoc */
    public function bound(string $type): bool
    {
        return isset($this->bindings[$type]);
    }

    /** @inheritdoc */
    public function unbind(string $type): void
    {
        unset($this->bindings[$type]);
    }

    /** @inheritdoc */
    public function addBinding(string $type, BindingInterface $binding): void
    {
        $this->bindings[$type] = $binding;
    }

    /** @inheritdoc */
    public function getBinding(string $type): ?BindingInterface
    {
        return $this->bindings[$type] ?? null;
    }

    /** @inheritdoc */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /** @inheritdoc */
    public function resetScoped(): void
    {
        foreach ($this->bindings as $binding) {
            if ($binding instanceof HasLifecycleInterface && $binding->is(Lifecycle::Scoped)) {
                $binding->reset();
            }
        }
    }
}
