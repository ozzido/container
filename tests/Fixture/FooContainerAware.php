<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

use Ozzido\Container\ContainerAwareInterface;
use Ozzido\Container\ContainerAwareTrait;
use Ozzido\Container\ContainerInterface;

class FooContainerAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
