<?php

declare(strict_types=1);

namespace Ozzido\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use ReflectionParameter;
use Throwable;

use function sprintf;

final class DependencyResolutionException extends RuntimeException implements
    ContainerExceptionInterface,
    ExceptionInterface
{
    public static function new(ReflectionParameter $parameter, ?Throwable $previous = null): static
    {
        $parameterDeclaringClassName = $parameter->getDeclaringClass()?->getName();

        return new static(sprintf(
            'Cannot resolve dependency "$%s" for "%s%s()".',
            $parameter->getName(),
            $parameterDeclaringClassName ? $parameterDeclaringClassName . '::' : '',
            $parameter->getDeclaringFunction()->getName()
        ), 0, $previous);
    }
}
