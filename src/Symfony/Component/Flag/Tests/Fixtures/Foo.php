<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Flag\Tests\Fixtures;

/**
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class Foo
{
    const FLAG_A = 1;
    const FLAG_B = 2;
    const FLAG_C = 4;

    public static function getPrefixedFlags()
    {
        return array(
            array('FLAG_A', Bar::FLAG_A),
            array('FLAG_B', Bar::FLAG_B),
            array('FLAG_C', Bar::FLAG_C),
        );
    }
}
