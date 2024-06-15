<?php

declare(strict_types=1);

namespace Ozzido\Container\Test;

use Ozzido\Container\Binding\ConcreteBinding;
use Ozzido\Container\BindingRegistrar;
use Ozzido\Container\BindingRegistry;
use Ozzido\Container\BindingRegistryInterface;
use Ozzido\Container\Test\Fixture\Foo;
use Ozzido\Container\Test\Fixture\OtherFoo;
use Ozzido\Container\Test\Fixture\Qux;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Override;

class BindingRegistryTest extends TestCase
{
    protected BindingRegistryInterface $bindings;

    #[Override]
    protected function setUp(): void
    {
        $this->bindings = new BindingRegistry();
    }

    #[Test]
    public function bindReturnsBindingRegistrar(): void
    {
        $this->assertInstanceOf(BindingRegistrar::class, $this->bindings->bind(Foo::class));
    }

    #[Test]
    public function addsAndGetsBinding(): void
    {
        $this->bindings->addBinding(Foo::class, new ConcreteBinding(Foo::class));
        $this->bindings->addBinding(OtherFoo::class, new ConcreteBinding(OtherFoo::class));
        $this->assertInstanceOf(ConcreteBinding::class, $this->bindings->getBinding(Foo::class));
        $this->assertInstanceOf(ConcreteBinding::class, $this->bindings->getBinding(OtherFoo::class));
        $this->assertNull($this->bindings->getBinding(Qux::class));
    }

    #[Test]
    public function checksBindingIsBound(): void
    {
        $this->bindings->addBinding(Foo::class, new ConcreteBinding(Foo::class));
        $this->assertTrue($this->bindings->bound(Foo::class));
        $this->assertFalse($this->bindings->bound(OtherFoo::class));
    }

    #[Test]
    public function unbindsBindings(): void
    {
        $this->bindings->addBinding(Foo::class, new ConcreteBinding(Foo::class));
        $this->assertTrue($this->bindings->bound(Foo::class));
        $this->bindings->unbind(Foo::class);
        $this->assertFalse($this->bindings->bound(Foo::class));
    }

    #[Test]
    public function getsBindings(): void
    {
        $expects = [
            Foo::class => new ConcreteBinding(Foo::class),
            OtherFoo::class => new ConcreteBinding(OtherFoo::class),
        ];

        $this->bindings->addBinding(Foo::class, $expects[Foo::class]);
        $this->bindings->addBinding(OtherFoo::class, $expects[OtherFoo::class]);
        $this->assertSame($this->bindings->getBindings(), $expects);
    }
}
