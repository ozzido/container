<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding\Capability;

use Ozzido\Container\Binding\Lifecycle;
use Ozzido\Container\Test\Fixture\Foo;
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
    public function definesLifecycleAsScoped(): void
    {
        $this->assertTrue($this->trait->asScoped()->is(Lifecycle::Scoped));
    }

    #[Test]
    public function definesLifecycleAsTransient(): void
    {
        $this->trait->asSingleton();
        $this->assertFalse($this->trait->is(Lifecycle::Transient));
        $this->assertTrue($this->trait->asTransient()->is(Lifecycle::Transient));
    }

    #[Test]
    public function resetsResolvedInstance(): void
    {
        $this->trait->setResolved(true);
        $this->trait->setInstance(new Foo());
        $this->assertInstanceOf(Foo::class, $this->trait->getInstance());
        $this->assertTrue($this->trait->getResolved());

        $this->trait->reset();
        $this->assertFalse($this->trait->getResolved());
        $this->assertNull($this->trait->getInstance());
    }
}
