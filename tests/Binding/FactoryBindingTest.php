<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding;

use Ozzido\Container\Binding\Capability\HasArgumentTrait;
use Ozzido\Container\Binding\Capability\HasLifecycleTrait;
use Ozzido\Container\Binding\FactoryBinding;
use Ozzido\Container\ContainerInterface;
use Ozzido\Container\Test\Fixture\Foo;
use Ozzido\Container\Test\Fixture\Qux;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function class_uses;

class FactoryBindingTest extends TestCase
{
    #[Test]
    public function usesHasArgumentTrait(): void
    {
        $this->assertContains(HasArgumentTrait::class, class_uses(FactoryBinding::class));
    }

    #[Test]
    public function usesHasLifecycleTrait(): void
    {
        $this->assertContains(HasLifecycleTrait::class, class_uses(FactoryBinding::class));
    }

    #[Test]
    public function resolvesFromContainerWithDefinedArguments(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(function ($callable, $arguments) {
                $this->assertSame($arguments, ['one' => 1, 'two' => 2]);
            });

        (new FactoryBinding(fn () => new Qux(null, null)))
            ->withArguments(['one' => 1, 'two' => 2])
            ->resolve($container);
    }

    #[Test]
    public function resolvesSameInstanceInSingletonLifecycle(): void
    {
        $container = $this->createContainerMockForLifecycleTests(1);
        $binding = (new FactoryBinding(fn () => new Foo()))->asSingleton();
        $this->assertSame($binding->resolve($container), $binding->resolve($container));
    }

    #[Test]
    public function resolvesSameInstanceInScopedLifecycle(): void
    {
        $container = $this->createContainerMockForLifecycleTests(1);
        $binding = (new FactoryBinding(fn () => new Foo()))->asScoped();
        $this->assertSame($binding->resolve($container), $binding->resolve($container));
    }

    #[Test]
    public function resolvesDifferentInstanceInTransientLifecycle(): void
    {
        $container = $this->createContainerMockForLifecycleTests(2);
        $binding = new FactoryBinding(fn () => new Foo());
        $this->assertNotSame($binding->resolve($container), $binding->resolve($container));
    }

    private function createContainerMockForLifecycleTests(int $exactly): ContainerInterface
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->exactly($exactly))
            ->method('call')
            ->willReturnCallback(fn ($callable) => $callable());

        return $container;
    }
}
