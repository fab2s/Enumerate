<?php

/*
 * This file is part of fab2s/Enumerate.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Enumerate
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerate;

use JsonSerializable;
use UnitEnum;

interface EnumerateInterface extends JsonSerializable, UnitEnum
{
    public static function tryFromAny(int|string|UnitEnum|null $value, bool $strict = true): ?static;

    public static function fromAny(int|string|UnitEnum|null $value, bool $strict = true): static;

    public static function tryFromName(?string $name): ?static;

    public static function fromName(string $name): static;

    public function equals(int|string|UnitEnum|null ...$cases): bool;

    public function compares(int|string|UnitEnum|null ...$cases): bool;

    public function toValue(): string|int;
}
