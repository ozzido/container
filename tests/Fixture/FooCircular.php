<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class FooCircular
{
    /** @phpstan-ignore-next-line */
    public function __construct(private FooCircular $foo)
    {
    }
}
