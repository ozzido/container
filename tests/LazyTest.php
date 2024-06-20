<?php

declare(strict_types=1);

namespace Ozzido\Container\Test;

use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Lazy;
use Ozzido\Container\Test\Fixture\Foo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LazyTest extends TestCase
{
    #[Test]
    public function takesFromContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($type) {
                $this->assertSame(Foo::class, $type);
            });

        (new Lazy(Foo::class))->resolve($container);
    }

    #[Test]
    public function takesUsingClosure(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->assertInstanceOf(Foo::class, (new Lazy(fn () => new Foo()))->resolve($container));
    }
}
