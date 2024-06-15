<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding\Capability;

use Ozzido\Container\Binding\Lifecycle;
use Ozzido\Container\Test\Fixture\HasLifecycleTraitWrapper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Override;

class HasLifecycleTraitTest extends TestCase
{
    protected HasLifecycleTraitWrapper $trait;

    #[Override]
    protected function setUp(): void
    {
        $this->trait = new HasLifecycleTraitWrapper();
    }

    #[Test]
    public function defaultLifecycleIsTransient(): void
    {
        $this->assertTrue($this->trait->is(Lifecycle::Transient));
    }

    #[Test]
    public function definesLifecycleAsSingleton(): void
    {
        $this->assertTrue($this->trait->asSingleton()->is(Lifecycle::Singleton));
    }

    #[Test]
    public function definesLifecycleAsTransient(): void
    {
        $this->trait->asSingleton();
        $this->assertFalse($this->trait->is(Lifecycle::Transient));
        $this->assertTrue($this->trait->asTransient()->is(Lifecycle::Transient));
    }
}
