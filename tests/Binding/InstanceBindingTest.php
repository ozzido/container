<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding;

use Ozzido\Container\Binding\InstanceBinding;
use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Test\Fixture\Foo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InstanceBindingTest extends TestCase
{
    #[Test]
    public function resolvesInstance(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->assertSame((new InstanceBinding($foo = new Foo()))->resolve($container), $foo);
    }
}
