<?php

declare(strict_types=1);

namespace Ozzido\Container;

trait ContainerAwareTrait
{
    private ContainerInterface $container;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
