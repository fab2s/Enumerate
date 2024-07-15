<?php

/*
 * This file is part of fab2s/Enumerate.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/Enumerate
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerated\Tests\Artifacts;

use fab2s\Enumerated\EnumerateInterface;
use fab2s\Enumerated\EnumerateTrait;

enum StringBackedEnum: string implements EnumerateInterface
{
    use EnumerateTrait;

    case ONE   = 'ONE';
    case TWO   = 'TWO';
    case three = 'three';
}
