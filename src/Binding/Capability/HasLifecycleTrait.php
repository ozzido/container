<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding\Capability;

use Ozzido\Container\Binding\Lifecycle;

trait HasLifecycleTrait
{
    private Lifecycle $lifecycle = Lifecycle::Transient;
    private bool $resolved = false;
    private mixed $instance = null;

    /** @inheritdoc */
    public function asSingleton(): static
    {
        return $this->as(Lifecycle::Singleton);
    }

    /** @inheritdoc */
    public function asTransient(): static
    {
        return $this->as(Lifecycle::Transient);
    }

    /** @inheritdoc */
    public function as(Lifecycle $lifecycle): static
    {
        $this->lifecycle = $lifecycle;

        return $this;
    }

    /** @inheritdoc */
    public function is(Lifecycle $lifecycle): bool
    {
        return $this->lifecycle === $lifecycle;
    }
}
