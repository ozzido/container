<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding\Capability;

use Ozzido\Container\Lazy;
use Closure;

trait HasArgumentTrait
{
    /** @var array<non-empty-string, mixed> */
    private array $arguments = [];

    /**
     * Defines a named constructor argument.
     *
     * @param non-empty-string $argument
     */
    public function withArgument(string $argument, mixed $value): static
    {
        $this->arguments[$argument] = $value;

        return $this;
    }

    /**
     * Defines a named constructor argument with lazy resolution.
     *
     * @param non-empty-string $argument
     * @param non-empty-string|class-string|Closure $typeOrFactory
     */
    public function withLazyArgument(string $argument, string|Closure $typeOrFactory): static
    {
        return $this->withArgument($argument, new Lazy($typeOrFactory));
    }

    /**
     * Defines multiple named constructor arguments.
     *
     * @param array<non-empty-string, mixed> $arguments
     */
    public function withArguments(array $arguments): static
    {
        foreach ($arguments as $argument => $value) {
            $this->withArgument($argument, $value);
        }

        return $this;
    }
}
