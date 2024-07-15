<?php

/*
 * This file is part of fab2s/Enumerate.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Enumerate
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerate;

use BackedEnum;
use InvalidArgumentException;
use ReflectionEnum;
use ReflectionException;
use UnitEnum;

class Enumerate
{
    protected static array $types = [];

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function tryFromAny(UnitEnum|string $enum, int|string|UnitEnum|null $value, bool $strict = true): UnitEnum|BackedEnum|null
    {
        if ($value === null) {
            return null;
        }

        if (is_object($value)) {
            if ($value instanceof $enum) {
                return $value;
            }

            if ($strict) {
                return null;
            }

            $value = static::toValue($value);
        }

        return match (true) {
            static::isStringBacked($enum) => is_string($value) ? $enum::tryFrom($value) : null,
            static::isIntBacked($enum)    => is_int($value) ? $enum::tryFrom($value) : null,
            default                       => static::tryFromName($enum, $value),
        };
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function fromAny(UnitEnum|string $enum, int|string|UnitEnum|null $value, bool $strict = true): UnitEnum|BackedEnum
    {
        return static::tryFromAny($enum, $value, $strict) ?? throw new InvalidArgumentException('Argument $value is not a valid enum case for ' . static::toEnumFqn($enum));
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     */
    public static function tryFromName(UnitEnum|string $enum, ?string $name): UnitEnum|BackedEnum|null
    {
        foreach ($enum::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     */
    public static function fromName(UnitEnum|string $enum, string $name): UnitEnum|BackedEnum
    {
        return static::tryFromName($enum, $name) ?? throw new InvalidArgumentException('Argument $name is not a valid enum case for ' . static::toEnumFqn($enum));
    }

    public static function toEnumFqn(UnitEnum|string $enum): string
    {
        return is_string($enum) ? $enum : $enum::class;
    }

    /**
     * @throws ReflectionException
     */
    public static function equals(UnitEnum $enum, int|string|UnitEnum|null ...$cases): bool
    {
        $value = static::toValue($enum);
        foreach ($cases as $case) {
            if ($value === static::toValue(static::tryFromAny($enum, $case))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ReflectionException
     */
    public static function compares(UnitEnum $enum, int|string|UnitEnum|null ...$cases): bool
    {
        foreach ($cases as $case) {
            if (static::equals($enum, static::tryFromAny($enum, $case, false))) {
                return true;
            }
        }

        return false;
    }

    public static function toValue(?UnitEnum $enum): string|int|null
    {
        return $enum ? ($enum->value ?? $enum->name) : null;
    }

    /**
     * until is_subclass_of is able to work with enums ...
     *
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function inspect(UnitEnum|string $enum): array
    {
        return static::$types[static::toEnumFqn($enum)] ??= [
            'type'             => $type = ((string) (new ReflectionEnum($enum))->getBackingType()) ?: null,
            'is_string_backed' => $type === 'string',
            'is_int_backed'    => $type === 'int',
            'is_backed'        => (bool) $type,
        ];
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function getType(UnitEnum|string $enum): ?string
    {
        return static::inspect($enum)['type'];
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function isStringBacked(UnitEnum|string $enum): bool
    {
        return static::inspect($enum)['is_string_backed'];
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function isIntBacked(UnitEnum|string $enum): bool
    {
        return static::inspect($enum)['is_int_backed'];
    }

    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function isBacked(UnitEnum|string $enum): bool
    {
        return static::inspect($enum)['is_backed'];
    }
}
