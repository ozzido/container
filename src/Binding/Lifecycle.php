<?php

declare(strict_types=1);

namespace Ozzido\Container\Binding;

enum Lifecycle
{
    case Singleton;
    case Transient;
}
