<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class Quux
{
    /** @var array<int|string, FooInterface> */
    private array $foos = [];

    public function __construct(FooInterface ...$foos)
    {
        $this->foos = $foos;
    }

    /** @return array<int|string, FooInterface> */
    public function getFoos(): array
    {
        return $this->foos;
    }
}
