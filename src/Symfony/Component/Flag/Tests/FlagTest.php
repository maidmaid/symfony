<?php

namespace Symfony\Component\Flag\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Flag\Flag;
use Symfony\Component\Flag\Tests\Fixtures\Bar;

class FlagTest extends TestCase
{
    /**
     * @dataProvider provideBitfields
     */
    public function testSet($bitfield)
    {
        $flag = (new Flag())->set($bitfield);

        $this->assertEquals($bitfield, $flag->get());
    }

    public function provideBitfields()
    {
        return array(
            array(0),
            array(1),
            array(2),
            array(4),
            array(1023),
            array(1024),
            array(E_ALL),
            array(PHP_INT_MAX),
        );
    }

    /**
     * @expectedException \Symfony\Component\Flag\Exception\InvalidArgumentException
     * @expectedExceptionMessage Bitfield must be an integer.
     */
    public function testSetNotIntBitfield()
    {
        (new Flag())->set('a');
    }

    /**
     * @expectedException \Symfony\Component\Flag\Exception\InvalidArgumentException
     * @expectedExceptionMessage Bitfield must not exceed integer max limit.
     */
    public function testSetToBigBitfield()
    {
        (new Flag())->set(PHP_INT_MAX * 2);
    }


    public function testGetFlags()
    {
        $flag = new Flag(Bar::class, 'FLAG_', Bar::FLAG_D);

        $flags = $flag->getFlags();
        foreach (Bar::getPrefixedFlags() as $expected) {
            $this->assertArrayHasKey($expected[0], $flags);
        }

        $flags = $flag->getFlags(true);
        $this->assertArrayHasKey('FLAG_D', $flags);
        $this->assertArrayNotHasKey('FLAG_E', $flags);
        $this->assertArrayNotHasKey('FLAG_F', $flags);
    }
}
