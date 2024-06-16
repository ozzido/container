<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\Binding\Capability\HasTagInterface;
use Ozzido\Container\Binding\Capability\HasTagTrait;
use Ozzido\Container\ContainerInterface;

final class InstanceBinding implements HasTagInterface, BindingInterface
{
    use HasTagTrait;

    public function __construct(private readonly object $instance)
    {
    }

    /** @inheritdoc */
    public function resolve(ContainerInterface $container): object
    {
        return $this->instance;
    }
}
