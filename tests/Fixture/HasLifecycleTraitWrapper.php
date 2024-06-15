<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

use Ozzido\Container\Binding\Capability\HasLifecycleInterface;
use Ozzido\Container\Binding\Capability\HasLifecycleTrait;

class HasLifecycleTraitWrapper implements HasLifecycleInterface
{
    use HasLifecycleTrait;

    public function setResolved(bool $resolved): void
    {
        $this->resolved = $resolved;
    }

    public function getResolved(): bool
    {
        return $this->resolved;
    }

    public function setInstance(mixed $instance): void
    {
        $this->instance = $instance;
    }

    public function getInstance(): mixed
    {
        return $this->instance;
    }
}
