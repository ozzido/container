<?php

declare(strict_types=1);

namespace Ozzido\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}