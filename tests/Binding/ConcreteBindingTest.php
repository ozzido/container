<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding;

use Ozzido\Container\Binding\Capability\HasArgumentTrait;
use Ozzido\Container\Binding\Capability\HasTagTrait;
use Ozzido\Container\Binding\Capability\HasLifecycleTrait;
use Ozzido\Container\Binding\ConcreteBinding;
use Ozzido\Container\Exception\MethodCallException;
use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Test\Fixture\Foo;
use Ozzido\Container\Test\Fixture\Qux;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function class_uses;

class ConcreteBindingTest extends TestCase
{
    #[Test]
    public function usesHasArgumentTrait(): void
    {
        $this->assertContains(HasArgumentTrait::class, class_uses(ConcreteBinding::class));
    }

    #[Test]
    public function usesHasTagTrait(): void
    {
        $this->assertContains(HasTagTrait::class, class_uses(ConcreteBinding::class));
    }

    #[Test]
    public function usesHasLifecycleTrait(): void
    {
        $this->assertContains(HasLifecycleTrait::class, class_uses(ConcreteBinding::class));
    }

    #[Test]
    public function resolvesFromContainerWithDefinedArguments(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('construct')
            ->willReturnCallback(function ($concrete, $arguments) {
                $this->assertSame($arguments, ['one' => 1, 'two' => 2]);

                return new $concrete(...$arguments);
            });

        (new ConcreteBinding(Qux::class))
            ->withArguments(['one' => 1, 'two' => 2])
            ->resolve($container);
    }

    #[Test]
    public function resolvesFromContainerWithDefinedMethodCalls(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('construct')
            ->willReturn(new Qux(null, null));

        $matcher = $this->exactly(2);

        $container
            ->expects($matcher)
            ->method('call')
            ->willReturnCallback(function ($callable, $arguments) use ($matcher) {
                if ($matcher->numberOfInvocations() == 1) {
                    $this->assertSame($callable[1], 'setOne');
                    $this->assertSame($arguments, ['one' => 1]);
                } elseif ($matcher->numberOfInvocations() == 2) {
                    $this->assertSame($callable[1], 'setTwo');
                    $this->assertSame($arguments, ['two' => 2]);
                }
            });

        (new ConcreteBinding(Qux::class))
            ->withMethodCalls([
                'setOne' => ['one' => 1],
                'setTwo' => ['two' => 2],
            ])
            ->resolve($container);
    }

    #[Test]
    public function resolvesSameInstanceInSingletonLifecycle(): void
    {
        $container = $this->createContainerMockForLifecycleOrMethodCallTests(1);
        $binding = (new ConcreteBinding(Foo::class))->asSingleton();
        $this->assertSame($binding->resolve($container), $binding->resolve($container));
    }

    #[Test]
    public function resolvesSameInstanceInScopedLifecycle(): void
    {
        $container = $this->createContainerMockForLifecycleOrMethodCallTests(1);
        $binding = (new ConcreteBinding(Foo::class))->asScoped();
        $this->assertSame($binding->resolve($container), $binding->resolve($container));
    }

    #[Test]
    public function resolvesDifferentInstanceInTransientLifecycle(): void
    {
        $container = $this->createContainerMockForLifecycleOrMethodCallTests(2);
        $binding = new ConcreteBinding(Foo::class);
        $this->assertNotSame($binding->resolve($container), $binding->resolve($container));
    }

    #[Test]
    public function resolveThrowsMethodCallExceptionOnNonExistentMethod(): void
    {
        $this->expectException(MethodCallException::class);
        $this->expectExceptionMessage('Cannot execute "Ozzido\Container\Test\Fixture\Foo::nonExistentMethod()" method call. Method is does not exists or not invokable.');
        $container = $this->createContainerMockForLifecycleOrMethodCallTests(1);
        (new ConcreteBinding(Foo::class))->withMethodCall('nonExistentMethod')->resolve($container);
    }

    private function createContainerMockForLifecycleOrMethodCallTests(int $exactly): ContainerInterface
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->exactly($exactly))
            ->method('construct')
            ->willReturnCallback(fn ($concrete) => new $concrete());

        return $container;
    }
}
