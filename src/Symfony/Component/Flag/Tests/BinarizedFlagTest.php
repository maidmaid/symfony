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
}
