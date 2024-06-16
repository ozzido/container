<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Binding\Capability;

use Ozzido\Container\Test\Fixture\HasTagTraitWrapper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Override;

class HasTagTraitTest extends TestCase
{
    protected HasTagTraitWrapper $trait;

    #[Override]
    protected function setUp(): void
    {
        $this->trait = new HasTagTraitWrapper();
    }

    #[Test]
    public function definesTag(): void
    {
        $this->trait->withTag('one')->withTag('two');
        $this->assertTrue($this->trait->hasTag('one'));
        $this->assertTrue($this->trait->hasTag('one'));
    }

    #[Test]
    public function definesTags(): void
    {
        $this->trait->withTags(['one', 'two']);
        $this->assertTrue($this->trait->hasTag('one'));
        $this->assertTrue($this->trait->hasTag('one'));
    }
}
