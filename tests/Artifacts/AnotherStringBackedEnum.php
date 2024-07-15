<?php

/*
 * This file is part of fab2s/enumerated.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/enumerated
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerated\Tests\Artifacts;

use fab2s\Enumerated\Enumerated;
use JsonSerializable;

enum AnotherStringBackedEnum: string implements JsonSerializable
{
    use Enumerated;

    case ANOTHER_ONE   = 'ONE';
    case ANOTHER_TWO   = 'TWO';
    case another_three = 'three';
}
