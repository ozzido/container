<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding;

use Ozzido\Container\Binding\AliasBinding;
use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Test\Fixture\Foo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AliasBindingTest extends TestCase
{
    #[Test]
    public function resolvesAliasFromContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($type) {
                $this->assertSame(Foo::class, $type);
            });

        (new AliasBinding(Foo::class))->resolve($container);
    }
}
