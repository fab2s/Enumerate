<?php

/*
 * This file is part of fab2s/Enumerate.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Enumerate
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerate\Tests\Artifacts;

use fab2s\Enumerate\EnumerateInterface;
use fab2s\Enumerate\EnumerateTrait;

enum AnotherIntBackedEnum: int implements EnumerateInterface
{
    use EnumerateTrait;

    case ANOTHER_ONE   = 1;
    case ANOTHER_TWO   = 2;
    case another_three = 3;
}
