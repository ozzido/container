<?php

declare(strict_types=1);

namespace Ozzido\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

use function sprintf;

final class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface, ExceptionInterface
{
    public static function new(string $type): static
    {
        return new static(sprintf('Type "%s" is not found in the container.', $type));
    }
}
