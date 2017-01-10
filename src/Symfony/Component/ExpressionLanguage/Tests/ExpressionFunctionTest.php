<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ExpressionLanguage\Tests;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * Tests ExpressionFunction.
 *
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class ExpressionFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage PHP function "fn_does_not_exist" does not exist.
     */
    public function testFunctionDoesNotExist()
    {
        ExpressionFunction::fromPhp('fn_does_not_exist');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage An expression function name must be defined if PHP function "Symfony\Component\ExpressionLanguage\Tests\fn_namespaced" is in namespace.
     */
    public function testFunctionNamespaced()
    {
        ExpressionFunction::fromPhp('Symfony\Component\ExpressionLanguage\Tests\fn_namespaced');
    }
}

function fn_namespaced()
{
}
