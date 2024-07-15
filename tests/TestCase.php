<?php

/*
 * This file is part of fab2s/enumerated.
 * (c) Fabrice de Stefanis / https://github.com/fab2s/enumerated
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Enumerated\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        // Turn on error reporting
        error_reporting(E_ALL);
        parent::setUp();
    }
}
