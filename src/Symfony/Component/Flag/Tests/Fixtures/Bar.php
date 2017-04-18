<?php

namespace Symfony\Component\Flag\Tests\Fixtures;

class Bar extends Foo
{
    const A = 'a';
    const B = 'b';
    const C = 'c';

    public static function getNotPrefixedFlags()
    {
        return array(
            array('A', Bar::A),
            array('B', Bar::B),
            array('C', Bar::C),
        );
    }

    public static function getFlags()
    {
        return array_merge(self::getNotPrefixedFlags(), self::getPrefixedFlags());
    }
}
