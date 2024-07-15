<?php

/*
 * This file is part of fab2s/Enumerate.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Enumerate
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerate;

use InvalidArgumentException;
use ReflectionException;
use UnitEnum;

trait EnumerateTrait
{
    /**
     * @throws ReflectionException
     */
    public static function tryFromAny(int|string|UnitEnum|null $value, bool $strict = true): ?static
    {
        return Enumerate::tryFromAny(static::class, $value, $strict);
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public static function fromAny(int|string|UnitEnum|null $value, bool $strict = true): static
    {
        return Enumerate::fromAny(static::class, $value, $strict);
    }

    public static function tryFromName(?string $name): ?static
    {
        return Enumerate::tryFromName(static::class, $name);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromName(string $name): static
    {
        return Enumerate::fromName(static::class, $name);
    }

    /**
     * @throws ReflectionException
     */
    public function equals(int|string|UnitEnum|null ...$cases): bool
    {
        return Enumerate::equals($this, ...$cases);
    }

    /**
     * @throws ReflectionException
     */
    public function compares(int|string|UnitEnum|null ...$cases): bool
    {
        return Enumerate::compares($this, ...$cases);
    }

    public function toValue(): string|int
    {
        return $this->value ?? $this->name;
    }

    public function jsonSerialize(): int|string
    {
        return $this->toValue();
    }
}
