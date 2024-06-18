<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

use Ozzido\Container\Binding\Capability\HasArgumentTrait;
use Ozzido\Container\Binding\Capability\HasTagInterface;
use Ozzido\Container\Binding\Capability\HasTagTrait;
use Ozzido\Container\Binding\Capability\HasLifecycleInterface;
use Ozzido\Container\Binding\Capability\HasLifecycleTrait;
use Ozzido\Container\Exception\MethodCallException;
use Ozzido\Container\ContainerInterface;

use function method_exists;
use function is_callable;

final class ConcreteBinding implements HasTagInterface, HasLifecycleInterface, BindingInterface
{
    use HasArgumentTrait;
    use HasTagTrait;
    use HasLifecycleTrait;

    /** @var list<array{non-empty-string, array<non-empty-string, mixed>}> */
    private array $methodCalls = [];

    /**
     * @param class-string $concrete
     */
    public function __construct(private readonly string $concrete)
    {
    }

    /**
     * Defines a method call with named arguments.
     *
     * @param non-empty-string $method
     * @param array<non-empty-string, mixed> $arguments
     */
    public function withMethodCall(string $method, array $arguments = []): static
    {
        $this->methodCalls[] = [$method, $arguments];

        return $this;
    }

    /**
     * Defines multiple method calls with named arguments.
     *
     * @param array<non-empty-string, array<non-empty-string, mixed>> $methodCalls
     */
    public function withMethodCalls(array $methodCalls): static
    {
        foreach ($methodCalls as $methodCall => $arguments) {
            $this->withMethodCall($methodCall, $arguments);
        }

        return $this;
    }

    /** @inheritdoc */
    public function resolve(ContainerInterface $container): mixed
    {
        if (!$this->resolved || $this->lifecycle === Lifecycle::Transient) {
            $this->instance = $container->construct($this->concrete, $this->arguments);

            foreach ($this->methodCalls as [$method, $arguments]) {
                $callable = [$this->instance, $method];

                if (!method_exists($this->instance, $method) || !is_callable($callable)) {
                    throw MethodCallException::new($this->instance, $method);
                }

                $container->call($callable, $arguments, false);
            }

            $this->resolved = true;
        }

        return $this->instance;
    }
}
