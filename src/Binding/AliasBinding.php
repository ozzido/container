<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\ContainerInterface;

final class AliasBinding implements BindingInterface
{
    /**
     * @param non-empty-string|class-string $alias
     */
    public function __construct(private readonly string $alias)
    {
    }

    /** @inheritdoc */
    public function resolve(ContainerInterface $container): mixed
    {
        return $container->get($this->alias);
    }
}
