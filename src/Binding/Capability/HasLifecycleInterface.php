<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding\Capability;

use Ozzido\Container\Binding\Lifecycle;

interface HasLifecycleInterface
{
    /**
     * Defines the lifecycle of the binding as a singleton.
     */
    public function asSingleton(): static;

    /**
     * Defines the lifecycle of the binding as a transient.
     */
    public function asTransient(): static;

    /**
     * Defines the lifecycle of the binding.
     */
    public function as(Lifecycle $lifecycle): static;

    /**
     * Checks whether the binding matches the given lifecycle.
     */
    public function is(Lifecycle $lifecycle): bool;
}
