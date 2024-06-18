<?php

declare(strict_types=1);

namespace Ozzido\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

use function implode;
use function array_keys;
use function sprintf;

final class CircularDependencyException extends RuntimeException implements
    ContainerExceptionInterface,
    ExceptionInterface
{
    /**
     * @param array<class-string, bool> $constructStack
     */
    public static function new(string $concrete, array $constructStack): static
    {
        $message = 'Cannot resolve circular dependency for "%s" class. Construct stack: "%s".';

        return new static(sprintf($message, $concrete, implode('" -> "', [...array_keys($constructStack), $concrete])));
    }
}
