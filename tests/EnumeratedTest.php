<?php

/*
 * This file is part of fab2s/enumerated.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/enumerated
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerated\Tests;

use fab2s\Enumerated\Tests\Artifacts\AnotherIntBackedEnum;
use fab2s\Enumerated\Tests\Artifacts\AnotherStringBackedEnum;
use fab2s\Enumerated\Tests\Artifacts\IntBackedEnum;
use fab2s\Enumerated\Tests\Artifacts\StringBackedEnum;
use fab2s\Enumerated\Tests\Artifacts\UnitEnum;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

class EnumeratedTest extends TestCase
{
    public function test_is_methods(): void
    {
        $this->assertTrue(StringBackedEnum::isStringBacked());
        $this->assertFalse(StringBackedEnum::isIntBacked());
        $this->assertTrue(StringBackedEnum::isBacked());

        $this->assertFalse(IntBackedEnum::isStringBacked());
        $this->assertTrue(IntBackedEnum::isIntBacked());
        $this->assertTrue(IntBackedEnum::isBacked());

        $this->assertFalse(UnitEnum::isStringBacked());
        $this->assertFalse(UnitEnum::isIntBacked());
        $this->assertFalse(UnitEnum::isBacked());
    }

    public function test_equals(): void
    {
        $this->assertTrue(StringBackedEnum::ONE->equals(...StringBackedEnum::cases()));
        $this->assertTrue(StringBackedEnum::ONE->equals('ONE'));
        $this->assertFalse(StringBackedEnum::ONE->equals(1));

        $this->assertTrue(IntBackedEnum::ONE->equals(...IntBackedEnum::cases()));
        $this->assertTrue(IntBackedEnum::ONE->equals(1));
        $this->assertFalse(IntBackedEnum::ONE->equals('ONE'));
        $this->assertFalse(IntBackedEnum::ONE->equals('1'));

        $this->assertTrue(UnitEnum::ONE->equals(...UnitEnum::cases()));
        $this->assertTrue(UnitEnum::ONE->equals('ONE'));
        $this->assertFalse(UnitEnum::ONE->equals('TWO'));
    }

    public function test_compare(): void
    {
        $this->assertTrue(StringBackedEnum::ONE->compares(...StringBackedEnum::cases()));
        $this->assertTrue(StringBackedEnum::ONE->compares('ONE'));
        $this->assertTrue(StringBackedEnum::ONE->compares(...AnotherStringBackedEnum::cases()));
        $this->assertTrue(StringBackedEnum::ONE->compares(AnotherStringBackedEnum::ANOTHER_ONE));
        $this->assertFalse(StringBackedEnum::ONE->compares(1));

        $this->assertTrue(IntBackedEnum::ONE->compares(...IntBackedEnum::cases()));
        $this->assertTrue(IntBackedEnum::ONE->compares(1));
        $this->assertTrue(IntBackedEnum::ONE->compares('ONE'));
        $this->assertTrue(IntBackedEnum::ONE->compares(...AnotherIntBackedEnum::cases()));
        $this->assertTrue(IntBackedEnum::ONE->compares(AnotherIntBackedEnum::ANOTHER_ONE));
        $this->assertFalse(IntBackedEnum::ONE->compares('1'));

        $this->assertTrue(UnitEnum::ONE->compares(...UnitEnum::cases()));
        $this->assertTrue(UnitEnum::ONE->compares('ONE'));
        $this->assertFalse(UnitEnum::ONE->compares('TWO'));
    }

    /**
     * @param class-string<UnitEnum|StringBackedEnum|IntBackedEnum> $enumFqn
     */
    #[DataProvider('tryFromProvider')]
    public function test_try_from(
        string $enumFqn,
        int|string|\UnitEnum|null $value,
        ?\UnitEnum $expected,
        bool $strict = true,
    ): void {
        $this->assertSame($expected, $enumFqn::tryFromAny($value, $strict));

        if (! is_object($value)) {
            $this->assertSame($expected, $enumFqn::tryFromValue($value, $strict));
        }

        if ($expected === null) {
            try {
                $enumFqn::fromAny($value);
            } catch (Throwable $e) {
                $this->assertInstanceOf(InvalidArgumentException::class, $e);
            }

            if (! is_object($value)) {
                try {
                    $enumFqn::fromValue($value);
                } catch (Throwable $e) {
                    $this->assertInstanceOf(InvalidArgumentException::class, $e);
                }

                if (is_string($value)) {
                    try {
                        $enumFqn::fromName($value);
                    } catch (Throwable $e) {
                        $this->assertInstanceOf(InvalidArgumentException::class, $e);
                    }
                }
            }

            return;
        }

        if (! is_object($value)) {
            if (! $enumFqn::isIntBacked() || ! $strict) {
                $this->assertSame($expected, $enumFqn::fromName($value));
            }
        }

        $instance      = $enumFqn::tryFromAny($value, $strict);
        $expectedValue = $instance->value ?? $instance->name;
        $this->assertSame($expectedValue, $instance->toValue());
        $this->assertSame($expectedValue, $instance->jsonSerialize());
        $this->assertSame(json_encode($expectedValue), json_encode($instance));

    }

    public static function tryFromProvider(): array
    {
        return [
            'UnitEnum_null' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => null,
                'expected' => null,
            ],
            'UnitEnum_whatEver' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => 'whatEver',
                'expected' => null,
            ],
            'UnitEnum_42' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => 42,
                'expected' => null,
            ],
            'UnitEnum_instance' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => UnitEnum::ONE,
                'expected' => UnitEnum::ONE,
            ],
            'UnitEnum_ONE' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => 'ONE',
                'expected' => UnitEnum::ONE,
            ],
            'UnitEnum_Another_strict' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => AnotherStringBackedEnum::ANOTHER_ONE,
                'expected' => null,
            ],
            'UnitEnum_Another' => [
                'enumFqn'  => UnitEnum::class,
                'value'    => AnotherStringBackedEnum::ANOTHER_ONE,
                'expected' => UnitEnum::ONE,
                'strict'   => false,
            ],
            'StringBackedEnum_null' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => null,
                'expected' => null,
            ],
            'StringBackedEnum_whatEver' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => 'whatEver',
                'expected' => null,
            ],
            'StringBackedEnum_42' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => 42,
                'expected' => null,
            ],
            'StringBackedEnum_instance' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => StringBackedEnum::ONE,
                'expected' => StringBackedEnum::ONE,
            ],
            'StringBackedEnum_ONE' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => 'ONE',
                'expected' => StringBackedEnum::ONE,
            ],
            'StringBackedEnum_Another_strict' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => AnotherStringBackedEnum::ANOTHER_ONE,
                'expected' => null,
            ],
            'StringBackedEnum_Another' => [
                'enumFqn'  => StringBackedEnum::class,
                'value'    => AnotherStringBackedEnum::ANOTHER_ONE,
                'expected' => StringBackedEnum::ONE,
                'strict'   => false,
            ],
            'IntBackedEnum_null' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => null,
                'expected' => null,
            ],
            'IntBackedEnum_whatEver' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => 'whatEver',
                'expected' => null,
            ],
            'IntBackedEnum_42' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => 42,
                'expected' => null,
            ],
            'IntBackedEnum_instance' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => IntBackedEnum::ONE,
                'expected' => IntBackedEnum::ONE,
            ],
            'IntBackedEnum_ONE' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => 1,
                'expected' => IntBackedEnum::ONE,
            ],
            'IntBackedEnum_Another_strict' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => AnotherIntBackedEnum::ANOTHER_ONE,
                'expected' => null,
            ],
            'IntBackedEnum_Another' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => AnotherIntBackedEnum::ANOTHER_ONE,
                'expected' => IntBackedEnum::ONE,
                'strict'   => false,
            ],
            'IntBackedEnum_ONE_string' => [
                'enumFqn'  => IntBackedEnum::class,
                'value'    => 'ONE',
                'expected' => IntBackedEnum::ONE,
                'strict'   => false,
            ],
        ];
    }
}
