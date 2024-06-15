<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\ContainerInterface;

final class InstanceBinding implements BindingInterface
{
    public function __construct(private readonly object $instance)
    {
    }

    /** @inheritdoc */
    public function resolve(ContainerInterface $container): object
    {
        return $this->instance;
    }
}
