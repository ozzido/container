<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class DecoratedFoo implements FooInterface
{
    public function __construct(private FooInterface $foo)
    {
    }

    public function getDecorated(): FooInterface
    {
        return $this->foo;
    }
}
