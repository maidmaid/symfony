<?php

namespace Symfony\Component\Flag\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Flag\BinarizedFlag;
use Symfony\Component\Flag\Flag;
use Symfony\Component\Flag\Tests\Fixtures\Bar;
use Symfony\Component\Flag\Tests\Fixtures\Foo;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BinarizedFlagTest extends TestCase
{
    public function testAdd()
    {
        $flag = new BinarizedFlag(Bar::class);

        $flag->add(Bar::A);
        $this->assertEquals(1 /* Bar::A */, $flag->get());

        $flag->add(Bar::B);
        $this->assertEquals(1 /* Bar::A */ | 2 /* Bar::B */, $flag->get());

        $this->assertNotEquals(4 /* Bar::C */, $flag->get());
    }

    public function testAddStandalone()
    {
        $flag = new BinarizedFlag();

        $flag->add('a');
        $this->assertEquals(1 /* a */, $flag->get());

        $flag->add('b');
        $this->assertEquals(1 /* a */ | 2 /* b */, $flag->get());

        $this->assertNotEquals(4 /* c */, $flag->get());
    }

    public function testRemove()
    {
        $flag = (new BinarizedFlag(Bar::class))
            ->add(Bar::A)
            ->add(Bar::B)
        ;

        $flag->remove(Bar::B);
        $this->assertEquals(1 /* Bar::A */, $flag->get());

        $this->assertNotEquals(4 /* Bar::C */, $flag->get());
    }

    public function testHas()
    {
        $flag = (new BinarizedFlag(Bar::class))
            ->add(Bar::A)
            ->add(Bar::B)
        ;

        $this->assertTrue($flag->has(Bar::A));
        $this->assertTrue($flag->has(Bar::B));
        $this->assertFalse($flag->has(Bar::C));
    }
}
