<?php

namespace Symfony\Component\Flag\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Flag\BinarizedFlag;
use Symfony\Component\Flag\Flag;
use Symfony\Component\Flag\HierarchicalFlag;
use Symfony\Component\Flag\Tests\Fixtures\Bar;
use Symfony\Component\Flag\Tests\Fixtures\Foo;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HierarchicalFlagTest extends TestCase
{
    public function testHas()
    {
        $flag = new HierarchicalFlag(Foo::class, 'FLAG_', Foo::FLAG_B);

        $this->assertTrue($flag->has(Foo::FLAG_A));
        $this->assertTrue($flag->has(Foo::FLAG_B));
        $this->assertFalse($flag->has(Foo::FLAG_C));
    }
}
