<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class FooInvokable
{
    public function __invoke(): string
    {
        return 'success';
    }
}
