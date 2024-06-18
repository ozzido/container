<?php

declare(strict_types=1);

namespace Ozzido\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use BadMethodCallException;

use function sprintf;

final class MethodCallException extends BadMethodCallException implements
    ContainerExceptionInterface,
    ExceptionInterface
{
    public static function new(object $instance, string $method): static
    {
        $message = 'Cannot execute "%s::%s()" method call. Method is does not exists or not invokable.';

        return new static(sprintf($message, $instance::class, $method));
    }
}
