<?php

declare(strict_types=1);

namespace Ozzido\Container;

use Ozzido\Container\Binding\Capability\HasTagInterface;
use Ozzido\Container\Binding\BindingInterface;
use Ozzido\Container\Exception\CircularDependencyException;
use Ozzido\Container\Exception\ConstructException;
use Ozzido\Container\Exception\DependencyResolutionException;
use Ozzido\Container\Exception\NotFoundException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use ReflectionNamedType;
use ReflectionException;
use Exception;
use Closure;

use function class_exists;
use function is_string;
use function is_array;
use function is_object;
use function str_contains;
use function array_key_exists;
use function explode;

/**
 * @phpstan-type ParameterCacheShape array{ReflectionParameter, ?class-string, bool}
 */
class Container implements ContainerInterface
{
    private readonly BindingRegistryInterface $bindings;
    /** @var array<class-string, BindingRegistryInterface> */
    private array $contextualBindings = [];
    /** @phpstan-var array<class-string, list<Closure(object, static): (object|void)>> */
    private array $interceptors = [];
    /** @var array<class-string, array<non-empty-string, ParameterCacheShape>> */
    private array $parameterCache = [];
    /** @var array<class-string, bool> */
    private array $constructStack = [];

    public function __construct()
    {
        $this->bindings = new BindingRegistry();

        $this->bind(Container::class)->toInstance($this);
        $this->bind(ContainerInterface::class)->toInstance($this);
    }

    /** @inheritdoc */
    public function for(string $concrete): BindingRegistryInterface
    {
        return $this->contextualBindings[$concrete] ??= new BindingRegistry();
    }

    /** @inheritdoc */
    public function bind(string $type): BindingRegistrar
    {
        return $this->bindings->bind($type);
    }

    /** @inheritdoc */
    public function bound(string $type): bool
    {
        return $this->bindings->bound($type);
    }

    /** @inheritdoc */
    public function unbind(string $type): void
    {
        $this->bindings->unbind($type);
    }

    /** @inheritdoc */
    public function addBinding(string $type, BindingInterface $binding): void
    {
        $this->bindings->addBinding($type, $binding);
    }

    /** @inheritdoc */
    public function getBinding(string $type): ?BindingInterface
    {
        return $this->bindings->getBinding($type);
    }

    /** @inheritdoc */
    public function getBindings(): array
    {
        return $this->bindings->getBindings();
    }

    /** @inheritdoc */
    public function resetScoped(): void
    {
        $this->bindings->resetScoped();

        foreach ($this->contextualBindings as $bindings) {
            $bindings->resetScoped();
        }
    }

    /** @inheritdoc */
    public function has(string $type): bool
    {
        return $this->bindings->bound($type) || class_exists($type);
    }

    /** @inheritdoc */
    public function get(string $type)
    {
        if ($binding = $this->bindings->getBinding($type)) {
            return $binding->resolve($this);
        }

        if (class_exists($type)) {
            return $this->construct($type);
        }

        throw NotFoundException::new($type);
    }

    /** @inheritdoc */
    public function getTagged(string $tag): array
    {
        $instances = [];

        foreach ($this->bindings->getBindings() as $binding) {
            if ($binding instanceof HasTagInterface && $binding->hasTag($tag)) {
                $instances[] = $binding->resolve($this);
            }
        }

        return $instances;
    }

    /** @inheritdoc */
    public function interceptor(string $type, Closure $interceptor): void
    {
        /** @phpstan-ignore-next-line */
        $this->interceptors[$type][] = $interceptor;
    }

    /** @inheritdoc */
    public function call(callable $callable, array $arguments = [], bool $intercept = true): mixed
    {
        if ($parameters = $this->getCallableParameters($callable)) {
            $instance = $callable(...$this->resolveDependencies($parameters, $arguments));
        } else {
            $instance = $callable();
        }

        if (!$intercept || !$this->interceptors || !is_object($instance)) {
            return $instance;
        }

        return $this->intercept($instance);
    }

    /** @inheritoc */
    public function construct(string $concrete, array $arguments = [], bool $intercept = true): object
    {
        if (isset($this->constructStack[$concrete])) {
            throw CircularDependencyException::new($concrete, $this->constructStack);
        }

        $this->constructStack[$concrete] = true;

        try {
            if ($parameters = $this->parameterCache[$concrete] ?? $this->getConstructorParameters($concrete)) {
                $instance = new $concrete(...$this->resolveDependencies($parameters, $arguments, $concrete));
            } else {
                $instance = new $concrete();
            }
        } finally {
            unset($this->constructStack[$concrete]);
        }

        if (!$intercept || !$this->interceptors) {
            return $instance;
        }

        return $this->intercept($instance);
    }

    /**
     * Returns a list of parameters for the given callable.
     *
     * @return array<non-empty-string, ParameterCacheShape>
     */
    private function getCallableParameters(callable $callable): array
    {
        if (is_string($callable) && str_contains($callable, '::')) {
            $callable = explode('::', $callable);
        } elseif (is_object($callable) && !($callable instanceof Closure)) {
            $callable = [$callable, '__invoke'];
        }

        if (is_array($callable)) {
            $function = new ReflectionMethod($callable[0], $callable[1]);
        } else {
            /** @var callable-string|Closure $callable */
            $function = new ReflectionFunction($callable);
        }

        return $this->prepareForParameterCache($function->getParameters());
    }

    /**
     * Returns a list of constructor parameters for the given concrete class.
     *
     * @param class-string $concrete
     * @return array<non-empty-string, ParameterCacheShape>
     */
    private function getConstructorParameters(string $concrete): array
    {
        try {
            $class = new ReflectionClass($concrete);
            /** @phpstan-ignore-next-line */
        } catch (ReflectionException $e) {
            throw ConstructException::newCannotReflect($concrete, $e);
        }

        if (!$class->isInstantiable()) {
            throw ConstructException::newCannotInstantiate($concrete);
        }

        $parameters = $class->getConstructor()?->getParameters() ?? [];

        return $this->parameterCache[$concrete] = $this->prepareForParameterCache($parameters);
    }

    /**
     * Prepares and optimizes the reflected parameters structure for the cache.
     *
     * @param list<ReflectionParameter> $parameters
     * @return array<non-empty-string, ParameterCacheShape>
     */
    private function prepareForParameterCache(array $parameters): array
    {
        $prepared = [];

        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType();
            $parameterTypeName = null;

            if ($parameterType instanceof ReflectionNamedType && !$parameterType->isBuiltin()) {
                /** @var class-string $parameterTypeName */
                $parameterTypeName = $parameterType->getName();
            }

            $prepared[$parameter->getName()] = [$parameter, $parameterTypeName, $parameter->isVariadic()];
        }

        return $prepared;
    }

    /**
     * Resolves dependencies for the given reflected parameters.
     *
     * @param array<non-empty-string, ParameterCacheShape> $parameters
     * @param array<non-empty-string, mixed> $overrides
     * @return list<mixed>
     */
    private function resolveDependencies(array $parameters, array $overrides = [], ?string $context = null): array
    {
        $dependencies = [];

        foreach ($parameters as $parameterName => [$parameter, $parameterType, $isVariadic]) {
            if (array_key_exists($parameterName, $overrides)) {
                $dependency = $overrides[$parameterName];

                if ($dependency instanceof Lazy) {
                    try {
                        $dependency = $dependency->resolve($this);
                    } catch (NotFoundException $e) {
                        throw DependencyResolutionException::new($parameter, $e);
                    }
                }
            } elseif ($parameterType) {
                $dependency = $this->resolveTypeDependency($parameter, $parameterType, $context);
            } else {
                $dependency = $this->resolveDependencyFromDefaults($parameter);
            }

            if ($isVariadic && is_array($dependency)) {
                $dependencies = array_merge($dependencies, $dependency);
            } else {
                $dependencies[] = $dependency;
            }
        }

        return $dependencies;
    }

    /**
     * @param class-string $type
     */
    private function resolveTypeDependency(ReflectionParameter $parameter, string $type, ?string $context = null): mixed
    {
        try {
            if ($context) {
                if ($bindings = $this->contextualBindings[$context] ?? null) {
                    if ($binding = $bindings->getBinding($type)) {
                        return $binding->resolve($this);
                    }
                }
            }

            return $this->get($type);
        } catch (NotFoundException $e) {
            return $this->resolveDependencyFromDefaults($parameter, $e);
        }
    }

    private function resolveDependencyFromDefaults(ReflectionParameter $parameter, ?Exception $e = null): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        if ($parameter->isVariadic()) {
            return [];
        }

        throw DependencyResolutionException::new($parameter, $e);
    }

    /**
     * @template T of object
     * @param T $instance
     * @return T
     */
    private function intercept(object $instance): object
    {
        foreach ($this->interceptors as $type => $interceptors) {
            if ($instance instanceof $type) {
                foreach ($interceptors as $interceptor) {
                    $decorated = $interceptor($instance, $this);

                    if (is_object($decorated)) {
                        /** @var T $instance */
                        $instance = $decorated;
                    }
                }
            }
        }

        return $instance;
    }
}
