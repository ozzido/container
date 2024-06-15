<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class Baz
{
    public function __construct(private FooInterface $foo)
    {
    }

    public function getFoo(): FooInterface
    {
        return $this->foo;
    }
}
