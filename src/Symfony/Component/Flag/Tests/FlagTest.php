<?php

namespace Symfony\Component\Flag\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Flag\Flag;
use Symfony\Component\Flag\Tests\Fixtures\Bar;
use Symfony\Component\Flag\Tests\Fixtures\Foo;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FlagTest extends TestCase
{
    /**
     * @dataProvider provideBitfields
     */
    public function testSetAndGet($bitfield)
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
        $flag = new Flag(Foo::class, 'FLAG_', Bar::FLAG_A);

        $flags = $flag->getFlags();
        foreach (Foo::getPrefixedFlags() as $expected) {
            $this->assertArrayHasKey($expected[0], $flags);
        }

        $flags = $flag->getFlags(true);
        $this->assertArrayHasKey('FLAG_A', $flags);
        $this->assertArrayNotHasKey('FLAG_B', $flags);
        $this->assertArrayNotHasKey('FLAG_C', $flags);
    }

    /**
     * @dataProvider provideToString
     */
    public function testToString($from, $prefix, $bitfield, $expected)
    {
        $flag = new Flag($from, $prefix, $bitfield);

        $this->assertEquals($expected, (string) $flag);
    }

    public function provideToString()
    {
        return array(
            array(Foo::class, '', 0, '[bin: 0] [dec: 0] [flags: ]'),
            array(Foo::class, '', Foo::FLAG_A, '[bin: 1] [dec: 1] [flags: FLAG_A]'),
            array(Foo::class, '', Foo::FLAG_A | Foo::FLAG_B, '[bin: 11] [dec: 3] [flags: FLAG_A | FLAG_B]'),
            array(Foo::class, 'FLAG_', 0, '[bin: 0] [dec: 0] [FLAG_*: ]'),
            array(Foo::class, 'FLAG_', Foo::FLAG_A, '[bin: 1] [dec: 1] [FLAG_*: A]'),
            array(Foo::class, 'FLAG_', Foo::FLAG_A | Foo::FLAG_B, '[bin: 11] [dec: 3] [FLAG_*: A | B]'),
        );
    }
}
