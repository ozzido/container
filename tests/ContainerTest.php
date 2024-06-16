<?php

declare(strict_types=1);

namespace Ozzido\Container\Test;

use Ozzido\Container\Binding\ConcreteBinding;
use Ozzido\Container\Exception\ContainerException;
use Ozzido\Container\Exception\NotFoundException;
use Ozzido\Container\BindingRegistrar;
use Ozzido\Container\Container;
use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Give;
use Ozzido\Container\Test\Fixture\Foo;
use Ozzido\Container\Test\Fixture\FooCircular;
use Ozzido\Container\Test\Fixture\FooInterface;
use Ozzido\Container\Test\Fixture\FooInvokable;
use Ozzido\Container\Test\Fixture\FooWithMethods;
use Ozzido\Container\Test\Fixture\Bar;
use Ozzido\Container\Test\Fixture\Baz;
use Ozzido\Container\Test\Fixture\OtherFoo;
use Ozzido\Container\Test\Fixture\Qux;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Override;

class ContainerTest extends TestCase
{
    protected ContainerInterface $container;

    #[Override]
    protected function setUp(): void
    {
        $this->container = new Container();
    }

    #[Test]
    public function bindsItSelf(): void
    {
        $this->assertSame($this->container->get(Container::class), $this->container);
        $this->assertSame($this->container->get(ContainerInterface::class), $this->container);
    }

    #[Test]
    public function forReturnsSameBindingRegistryForSameClass(): void
    {
        $this->assertSame($this->container->for(Foo::class), $this->container->for(Foo::class));
    }

    #[Test]
    public function bindReturnsBindingRegistrar(): void
    {
        $this->assertInstanceOf(BindingRegistrar::class, $this->container->bind(Foo::class));
    }

    #[Test]
    public function addsAndGetsBinding(): void
    {
        $this->container->addBinding(Foo::class, new ConcreteBinding(Foo::class));
        $this->container->addBinding(OtherFoo::class, new ConcreteBinding(OtherFoo::class));
        $this->assertInstanceOf(ConcreteBinding::class, $this->container->getBinding(Foo::class));
        $this->assertInstanceOf(ConcreteBinding::class, $this->container->getBinding(OtherFoo::class));
        $this->assertNull($this->container->getBinding(Qux::class));
    }

    #[Test]
    public function checksBindingIsBound(): void
    {
        $this->assertTrue($this->container->bound(Container::class));
        $this->assertFalse($this->container->bound(Foo::class));
    }

    #[Test]
    public function unbindsBindings(): void
    {
        $this->assertTrue($this->container->bound(Container::class));
        $this->container->unbind(Container::class);
        $this->assertFalse($this->container->bound(Container::class));
    }

    #[Test]
    public function getsBindings(): void
    {
        $this->assertSame($this->container->getBindings(), [
            Container::class => $this->container->getBinding(Container::class),
            ContainerInterface::class => $this->container->getBinding(ContainerInterface::class),
        ]);
    }

    #[Test]
    public function resetsScopedBindings(): void
    {
        $this->container->bind(FooInterface::class)->to(Foo::class)->asScoped();
        $foo = $this->container->get(FooInterface::class);
        $this->container->resetScoped();
        $this->assertNotSame($foo, $this->container->get(FooInterface::class));
    }

    #[Test]
    public function resetsScopedBindingsForSeparateContext(): void
    {
        $this->container->for(Bar::class)->bind(FooInterface::class)->to(Foo::class)->asScoped();
        $foo = $this->container->get(Bar::class)->getFoo();
        $this->container->resetScoped();
        $this->assertNotSame($foo, $this->container->get(Bar::class)->getFoo());
    }

    #[Test]
    public function checksHas(): void
    {
        $this->assertTrue($this->container->has(Container::class));
        $this->assertFalse($this->container->has(FooInterface::class));
        $this->container->bind(FooInterface::class)->to(Foo::class);
        $this->assertTrue($this->container->has(FooInterface::class));
    }

    #[Test]
    public function getsTagged(): void
    {
        $this->container->bind(Foo::class)->toSelf()->withTag('tag1');
        $this->container->bind(OtherFoo::class)->toSelf()->withTags(['tag1', 'tag2']);
        $this->container->bind(Qux::class)->toSelf()->withTag('tag2');

        $tagged = $this->container->getTagged('tag1');
        $this->assertInstanceOf(Foo::class, $tagged[0]);
        $this->assertInstanceOf(OtherFoo::class, $tagged[1]);

        $tagged = $this->container->getTagged('tag2');
        $this->assertInstanceOf(OtherFoo::class, $tagged[0]);
        $this->assertInstanceOf(Qux::class, $tagged[1]);
    }

    #[Test]
    public function bindsAndGetsToAlias(): void
    {
        $this->container->bind(FooInterface::class)->to(Foo::class)->asSingleton();
        $this->container->bind(Foo::class)->toAlias(FooInterface::class);
        $this->assertSame($this->container->get(FooInterface::class), $this->container->get(Foo::class));
    }

    #[Test]
    public function bindsAndGetsToInstance(): void
    {
        $foo = new Foo();
        $this->container->bind(FooInterface::class)->toInstance($foo);
        $this->assertSame($this->container->get(FooInterface::class), $foo);
    }

    #[Test]
    public function bindsAndGetsToConcreteWithDefinedArguments(): void
    {
        $arguments = ['one' => 1, 'two' => 2];
        $this->container->bind(Qux::class)->toSelf()->withArguments($arguments);
        $qux = $this->container->get(Qux::class);
        $this->assertSame($qux->getOne(), $arguments['one']);
        $this->assertSame($qux->getTwo(), $arguments['two']);
    }

    #[Test]
    public function bindsAndGetsToConcreteWithMethodDependencyResolving(): void
    {
        $this->container->bind(FooInterface::class)->to(Foo::class);
        $this->container->bind(Qux::class)->toSelf()->withMethodCall('setFoo');
        $this->assertInstanceOf(Foo::class, $this->container->get(Qux::class)->getFoo());
    }

    #[Test]
    public function bindsAndGetsToFactoryWithDefinedArguments(): void
    {
        $callable = fn (int $one, int $two): Qux => new Qux($one, $two);
        $arguments = ['one' => 1, 'two' => 2];
        $this->container->bind(Qux::class)->toFactory($callable)->withArguments($arguments);
        $qux = $this->container->get(Qux::class);
        $this->assertSame($qux->getOne(), $arguments['one']);
        $this->assertSame($qux->getTwo(), $arguments['two']);
    }

    #[Test]
    public function bindsAndGetsForSeparateContext(): void
    {
        $this->container->for(Bar::class)->bind(FooInterface::class)->to(Foo::class);
        $this->container->for(Baz::class)->bind(FooInterface::class)->to(OtherFoo::class);
        $this->assertInstanceOf(Foo::class, $this->container->get(Bar::class)->getFoo());
        $this->assertInstanceOf(OtherFoo::class, $this->container->get(Baz::class)->getFoo());
    }

    #[Test]
    public function callsClosure(): void
    {
        $this->assertSame($this->container->call(fn () => 'success'), 'success');
    }

    #[Test]
    public function callsFunction(): void
    {
        $this->assertSame($this->container->call('Ozzido\Container\Test\Fixture\testFunction'), 'success');
    }

    #[Test]
    public function callsStaticMethod(): void
    {
        $this->assertSame($this->container->call(FooWithMethods::class . '::staticPlain'), 'success');
    }

    #[Test]
    public function callsInstanceMethod(): void
    {
        $this->assertSame($this->container->call([new FooWithMethods(), 'plain']), 'success');
    }

    #[Test]
    public function callsInvokableObject(): void
    {
        $this->assertSame($this->container->call(new FooInvokable()), 'success');
    }

    #[Test]
    public function callsWithOverridedArgument(): void
    {
        $this->assertSame($this->container->call([new FooWithMethods(), 'withArgument'], ['argument' => 'success']), 'success');
    }

    #[Test]
    public function callsWithDependencyResolving(): void
    {
        $this->assertInstanceOf(Foo::class, $this->container->call([new FooWithMethods(), 'withClassArgument']));
    }

    #[Test]
    public function callsWithDependencyResolvingUsingGive(): void
    {
        $this->assertInstanceOf(Foo::class, $this->container->call([new FooWithMethods(), 'withClassArgument'], ['foo' => new Give(Foo::class)]));
    }

    #[Test]
    public function callsWithAllowNullArgument(): void
    {
        $this->assertNull($this->container->call([new FooWithMethods(), 'withAllowNullArgument']));
    }

    #[Test]
    public function callsWithDefaultArgument(): void
    {
        $this->assertSame($this->container->call([new FooWithMethods(), 'withDefaultArgument']), 'default');
    }

    #[Test]
    public function callsWithVariableArgument(): void
    {
        $this->assertSame($this->container->call([new FooWithMethods(), 'withVariableArgument']), []);
    }

    #[Test]
    public function constructThrowsContainerExceptionOnNonExistentClass(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Cannot reflect on "Ozzido\Container\Test\NonExistent" class.');
        /** @phpstan-ignore-next-line */
        $this->container->construct(NonExistent::class);
    }

    #[Test]
    public function constructThrowsContainerExceptionOnNotInstantiableClass(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Cannot instantiate not instantiable class "Ozzido\Container\Test\Fixture\FooInterface".');
        $this->container->construct(FooInterface::class);
    }

    #[Test]
    public function constructThrowsContainerExceptionnOnCircularDependency(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Cannot resolve circular dependency for "Ozzido\Container\Test\Fixture\FooCircular" class.');
        $this->container->construct(FooCircular::class);
    }

    #[Test]
    public function callThrowsNotFoundExceptionOnUnresorvableDependency(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Cannot resolve dependency "$nonExistent" for "Ozzido\Container\Test\Fixture\FooWithMethods::withUnresorvableClassArgument()".');
        $this->container->call([new FooWithMethods(), 'withUnresorvableClassArgument']);
    }

    #[Test]
    public function callThrowsNotFoundExceptionWhenGiveSpecifiesDependecyOnNonExistentClass(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Cannot resolve dependency "$foo" for "Ozzido\Container\Test\Fixture\FooWithMethods::withClassArgument()".');
        /** @phpstan-ignore-next-line */
        $this->container->call([new FooWithMethods(), 'withClassArgument'], ['foo' => new Give(NonExistent::class)]);
    }

    #[Test]
    public function getThrowsNotFoundExceptionOnNonExistentClass(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Class "Ozzido\Container\Test\NonExistent" does not exists.');
        /** @phpstan-ignore-next-line */
        $this->container->get(NonExistent::class);
    }

    #[Test]
    public function getThrowsNotFoundExceptionWhenBindsToNonExistentAlias(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Class "Ozzido\Container\Test\Fixture\FooInterface" does not exists.');
        $this->container->bind(Foo::class)->toAlias(FooInterface::class);
        $this->container->get(Foo::class);
    }
}
