<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\ContainerInterface;

interface BindingInterface
{
    /**
     * Resolves the binding.
     */
    public function resolve(ContainerInterface $container): mixed;
}
