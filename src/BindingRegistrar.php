<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Binding\BindingInterface;
use Ozzido\Container\Binding\ConcreteBinding;
use Ozzido\Container\Binding\FactoryBinding;
use Ozzido\Container\Binding\InstanceBinding;
use Ozzido\Container\Binding\AliasBinding;
use Closure;

readonly class BindingRegistrar
{
    /**
     * @param non-empty-string|class-string $type
     */
    public function __construct(private string $type, private BindingRegistryInterface $bindings)
    {
    }

    /**
     * Binds a type to itself.
     */
    public function toSelf(): ConcreteBinding
    {
        /** @phpstan-ignore-next-line */
        return $this->to($this->type);
    }

    /**
     * Binds a type to a concrete implementation.
     *
     * @param class-string $concrete
     */
    public function to(string $concrete): ConcreteBinding
    {
        return $this->toBinding(new ConcreteBinding($concrete));
    }

    /**
     * Binds a type to a factory that will return an instance of a concrete implementation.
     */
    public function toFactory(Closure $factory): FactoryBinding
    {
        return $this->toBinding(new FactoryBinding($factory));
    }

    /**
     * Binds a type to an already existing instance.
     */
    public function toInstance(object $instance): void
    {
        $this->toBinding(new InstanceBinding($instance));
    }

    /**
     * Binds a type as an alias to another existing binding.
     *
     * @param non-empty-string|class-string $alias
     */
    public function toAlias(string $alias): void
    {
        $this->toBinding(new AliasBinding($alias));
    }

    /**
     * Binds a type to the given custom binding.
     *
     * @template T of BindingInterface
     * @phpstan-param T $binding
     * @return T
     */
    public function toBinding(BindingInterface $binding): BindingInterface
    {
        $this->bindings->addBinding($this->type, $binding);

        return $binding;
    }
}
