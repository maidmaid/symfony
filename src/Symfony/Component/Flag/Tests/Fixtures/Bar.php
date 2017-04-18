<?php

namespace Symfony\Component\Flag\Tests\Fixtures;

class Bar
{
    const A = 'a';
    const B = 'b';
    const C = 'c';

    const FLAG_D = 1;
    const FLAG_E = 2;
    const FLAG_F = 4;

    public static function getNotPrefixedFlags()
    {
        return array(
            array('A', Bar::A),
            array('B', Bar::B),
            array('C', Bar::C),
        );
    }

    public static function getPrefixedFlags()
    {
        return array(
            array('FLAG_D', Bar::FLAG_D),
            array('FLAG_E', Bar::FLAG_E),
            array('FLAG_F', Bar::FLAG_F),
        );
    }

    public static function getFlags()
    {
        return array_merge(self::getNotPrefixedFlags(), self::getPrefixedFlags());
    }
}
