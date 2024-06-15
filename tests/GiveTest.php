<?php

declare(strict_types=1);

namespace Ozzido\Container\Test;

use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Give;
use Ozzido\Container\Test\Fixture\Foo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GiveTest extends TestCase
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

        (new Give(Foo::class))->resolve($container);
    }

    #[Test]
    public function takesUsingClosure(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->assertInstanceOf(Foo::class, (new Give(fn () => new Foo()))->resolve($container));
    }
}
