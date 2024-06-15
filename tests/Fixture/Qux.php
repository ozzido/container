<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class Qux
{
    private FooInterface $foo;

    public function __construct(
        private ?int $one,
        private ?int $two
    ) {
    }

    public function setOne(int $one): void
    {
        $this->one = $one;
    }

    public function getOne(): ?int
    {
        return $this->one;
    }

    public function setTwo(int $two): void
    {
        $this->two = $two;
    }

    public function getTwo(): ?int
    {
        return $this->two;
    }

    public function setFoo(FooInterface $foo): void
    {
        $this->foo = $foo;
    }

    public function getFoo(): FooInterface
    {
        return $this->foo;
    }
}
