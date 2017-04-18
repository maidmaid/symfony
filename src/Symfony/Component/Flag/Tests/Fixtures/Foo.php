<?php

namespace Symfony\Component\Flag\Tests\Fixtures;

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
