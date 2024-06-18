<?php

declare(strict_types=1);

namespace Ozzido\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use InvalidArgumentException;
use Throwable;

use function sprintf;

final class ConstructException extends InvalidArgumentException implements
    ContainerExceptionInterface,
    ExceptionInterface
{
    public static function newCannotReflect(string $concrete, ?Throwable $previous = null): static
    {
        return new static(sprintf('Cannot reflect on "%s" class.', $concrete), 0, $previous);
    }

    public static function newCannotInstantiate(string $concrete): static
    {
        return new static(sprintf('Cannot instantiate not instantiable "%s" class.', $concrete));
    }
}
