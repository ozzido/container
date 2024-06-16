<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\Binding\Capability\HasArgumentTrait;
use Ozzido\Container\Binding\Capability\HasTagInterface;
use Ozzido\Container\Binding\Capability\HasTagTrait;
use Ozzido\Container\Binding\Capability\HasLifecycleInterface;
use Ozzido\Container\Binding\Capability\HasLifecycleTrait;
use Ozzido\Container\ContainerInterface;
use Closure;

final class FactoryBinding implements HasTagInterface, HasLifecycleInterface, BindingInterface
{
    use HasArgumentTrait;
    use HasTagTrait;
    use HasLifecycleTrait;

    public function __construct(private readonly Closure $factory)
    {
    }

    /** @inheritdoc */
    public function resolve(ContainerInterface $container): mixed
    {
        if (!$this->resolved || $this->lifecycle === Lifecycle::Transient) {
            $this->instance = $container->call($this->factory, $this->arguments);
            $this->resolved = true;
        }

        return $this->instance;
    }
}
