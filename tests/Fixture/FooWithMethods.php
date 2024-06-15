<?php

declare(strict_types=1);

namespace Ozzido\Container\Test\Fixture;

class FooWithMethods
{
    public static function staticPlain(): string
    {
        return 'success';
    }

    public function plain(): string
    {
        return 'success';
    }

    public function withArgument(string $argument): string
    {
        return $argument;
    }

    public function withClassArgument(Foo $foo): Foo
    {
        return $foo;
    }

    /** @phpstan-ignore-next-line */
    public function withUnresorvableClassArgument(NonExistent $nonExistent): NonExistent
    {
        return $nonExistent;
    }

    public function withAllowNullArgument(mixed $allowNull): mixed
    {
        return $allowNull;
    }

    public function withDefaultArgument(string $default = 'default'): string
    {
        return $default;
    }

    /** @return array<int|string, string> */
    public function withVariableArgument(string ...$arguments): array
    {
        return $arguments;
    }
}
