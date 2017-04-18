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
class Bar extends Foo
{
    const A = 'a';
    const B = 'b';
    const C = 'c';

    public static function getNotPrefixedFlags()
    {
        return array(
            array('A', self::A),
            array('B', self::B),
            array('C', self::C),
        );
    }

    public static function getFlags()
    {
        return array_merge(self::getNotPrefixedFlags(), self::getPrefixedFlags());
    }
}
