<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding\Capability;

use Ozzido\Container\Lazy;
use Ozzido\Container\Test\Fixture\HasArgumentTraitWrapper;
use Ozzido\Container\Test\Fixture\Foo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Override;

class HasArgumentTraitTest extends TestCase
{
    protected HasArgumentTraitWrapper $trait;

    #[Override]
    protected function setUp(): void
    {
        $this->trait = new HasArgumentTraitWrapper();
    }

    #[Test]
    public function definesArgument(): void
    {
        $this->trait->withArgument('one', 1)->withArgument('two', 2);
        $this->assertSame($this->trait->getArguments(), ['one' => 1, 'two' => 2]);
    }

    #[Test]
    public function definesLazyArgument(): void
    {
        $this->trait->withLazyArgument('one', Foo::class);
        $this->assertInstanceOf(Lazy::class, $this->trait->getArguments()['one']);
    }

    #[Test]
    public function definesArguments(): void
    {
        $this->trait->withArguments(['one' => 1, 'two' => 2]);
        $this->assertSame($this->trait->getArguments(), ['one' => 1, 'two' => 2]);
    }
}
