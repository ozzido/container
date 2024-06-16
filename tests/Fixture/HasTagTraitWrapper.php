<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

use Ozzido\Container\Binding\Capability\HasTagInterface;
use Ozzido\Container\Binding\Capability\HasTagTrait;

class HasTagTraitWrapper implements HasTagInterface
{
    use HasTagTrait;
}
