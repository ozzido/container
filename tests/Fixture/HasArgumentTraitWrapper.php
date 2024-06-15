<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

use Ozzido\Container\Binding\Capability\HasArgumentTrait;

class HasArgumentTraitWrapper
{
    use HasArgumentTrait;

    /** @return array<string, mixed> */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
