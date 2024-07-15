<?php

/*
 * This file is part of fab2s/enumerated.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/enumerated
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerated;

use InvalidArgumentException;
use ReflectionEnum;
use UnitEnum;

trait Enumerated
{
    public static function tryFromAny(int|string|UnitEnum|null $value, bool $strict = true): ?static
    {
        if ($value === null) {
            return null;
        }

        if (is_object($value)) {
            if ($value instanceof static) {
                return $value;
            }

            if ($strict) {
                return null;
            }

            $value = $value->value ?? $value->name;
        }

        return match (true) {
            static::isStringBacked() => is_string($value) ? static::tryFrom($value) : null,
            static::isIntBacked()    => is_int($value) ? static::tryFrom($value) : ($strict ? null : static::tryFromName($value)),
            default                  => static::tryFromName($value),
        };
    }

    public static function fromAny(int|string|UnitEnum|null $value): static
    {
        return static::tryFromAny($value) ?? throw new InvalidArgumentException('Argument #1 ($value) is not a valid enum value.');
    }

    public static function tryFromName(?string $name): ?static
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    public static function fromName(string $name): static
    {
        return static::tryFromName($name) ?? throw new InvalidArgumentException('Argument #1 ($name) is not a valid enum value.');
    }

    public function equals(int|string|self|null ...$cases): bool
    {
        $value = $this->toValue();
        foreach ($cases as $case) {
            if ($value === static::tryFromAny($case)?->toValue()) {
                return true;
            }
        }

        return false;
    }

    public function compares(int|string|UnitEnum|null ...$cases): bool
    {
        foreach ($cases as $case) {
            if ($this->equals(static::tryFromAny($case, false))) {
                return true;
            }
        }

        return false;
    }

    public function toValue(): string|int
    {
        return $this->value ?? $this->name;
    }

    public static function fromValue(string|int|null $input): static
    {
        return static::fromAny($input);
    }

    public static function tryFromValue(string|int|null $input, bool $strict = true): ?static
    {
        return static::tryFromAny($input, $strict);
    }

    public function jsonSerialize(): int|string
    {
        return $this->toValue();
    }

    public static function getType(): ?string
    {
        static $types = [];

        return $types[static::class] ??= (string) (new ReflectionEnum(static::class))->getBackingType();
    }

    public static function isStringBacked(): bool
    {
        return static::getType() === 'string';
    }

    public static function isIntBacked(): bool
    {
        return static::getType() === 'int';
    }

    public static function isBacked(): bool
    {
        return (bool) static::getType();
    }
}
