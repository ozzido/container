<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\Exception\CircularDependencyException;
use Ozzido\Container\Exception\ConstructException;
use Ozzido\Container\Exception\DependencyResolutionException;
use Ozzido\Container\Exception\MethodCallException;
use Ozzido\Container\Exception\NotFoundException;
use Ozzido\Container\ContainerInterface;

interface BindingInterface
{
    /**
     * Resolves the binding.
     *
     * @throws CircularDependencyException
     * @throws ConstructException
     * @throws DependencyResolutionException
     * @throws MethodCallException
     * @throws NotFoundException
     */
    public function resolve(ContainerInterface $container): mixed;
}
