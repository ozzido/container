<?php

declare(strict_types=1);

namespace Ozzido\Container;

interface ContainerAwareInterface
{
    public function setContainer(ContainerInterface $container): void;
}
