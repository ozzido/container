<?php

declare(strict_types=1);

namespace Ozzido\Container\Test;

use Ozzido\Container\Binding\ConcreteBinding;
use Ozzido\Container\Binding\FactoryBinding;
use Ozzido\Container\Binding\InstanceBinding;
use Ozzido\Container\Binding\AliasBinding;
use Ozzido\Container\BindingRegistrar;
use Ozzido\Container\BindingRegistryInterface;
use Ozzido\Container\Test\Fixture\Foo;
use Ozzido\Container\Test\Fixture\FooInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Closure;

class BindingRegistrarTest extends TestCase
{
    #[Test]
    public function createsAndAddsConcreteBindingViaToSelf(): void
    {
        $bindings = $this->createBindingRegistryMock(function ($type, $binding) {
            $this->assertSame($type, Foo::class);
            $this->assertInstanceOf(ConcreteBinding::class, $binding);
        });

        (new BindingRegistrar(Foo::class, $bindings))->toSelf();
    }

    #[Test]
    public function createsAndAddsConcreteBindingViaTo(): void
    {
        $bindings = $this->createBindingRegistryMock(function ($type, $binding) {
            $this->assertSame($type, FooInterface::class);
            $this->assertInstanceOf(ConcreteBinding::class, $binding);
        });

        (new BindingRegistrar(FooInterface::class, $bindings))->to(Foo::class);
    }

    #[Test]
    public function createsAndAddsFactoryBinding(): void
    {
        $bindings = $this->createBindingRegistryMock(function ($type, $binding) {
            $this->assertSame($type, FooInterface::class);
            $this->assertInstanceOf(FactoryBinding::class, $binding);
        });

        (new BindingRegistrar(FooInterface::class, $bindings))->toFactory(fn () => new Foo());
    }

    #[Test]
    public function createsAndAddsInstanceBinding(): void
    {
        $bindings = $this->createBindingRegistryMock(function ($type, $binding) {
            $this->assertSame($type, FooInterface::class);
            $this->assertInstanceOf(InstanceBinding::class, $binding);
        });

        (new BindingRegistrar(FooInterface::class, $bindings))->toInstance(new Foo());
    }

    #[Test]
    public function createsAndAddsAliasBinding(): void
    {
        $bindings = $this->createBindingRegistryMock(function ($type, $binding) {
            $this->assertSame($type, FooInterface::class);
            $this->assertInstanceOf(AliasBinding::class, $binding);
        });

        (new BindingRegistrar(FooInterface::class, $bindings))->toAlias(Foo::class);
    }

    private function createBindingRegistryMock(Closure $callback): BindingRegistryInterface
    {
        $bindings = $this->createMock(BindingRegistryInterface::class);
        $bindings
            ->expects($this->once())
            ->method('addBinding')
            ->willReturnCallback($callback);

        return $bindings;
    }
}
