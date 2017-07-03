<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Tests\Caster;

use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\Caster\DateCaster;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Test\VarDumperTestTrait;

/**
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class DateCasterTest extends TestCase
{
    use VarDumperTestTrait;

    /**
     * @dataProvider provideDates
     */
    public function testDumpDate($time, $timezone, $expected)
    {
        if ((defined('HHVM_VERSION_ID') || PHP_VERSION_ID <= 50509) && preg_match('/[-+]\d{2}:\d{2}/', $timezone)) {
            $this->markTestSkipped('DateTimeZone GMT offsets are supported since 5.5.10. See https://github.com/facebook/hhvm/issues/5875 for HHVM.');
        }

        $date = new \DateTime($time, new \DateTimeZone($timezone));

        $xDump = <<<EODUMP
DateTime @1493503200 {
  date: $expected
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $date);
    }

    public function testCastDate()
    {
        $stub = new Stub();
        $date = new \DateTime('2017-08-30 00:00:00.000000', new \DateTimeZone('Europe/Zurich'));
        $cast = DateCaster::castDate($date, array('foo' => 'bar'), $stub, false, 0);

        $xDump = <<<'EODUMP'
array:1 [
  "\x00~\x00date" => 2017-08-30 00:00:00.000000 Europe/Zurich (+02:00)
]
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $cast);

        $xDump = <<<'EODUMP'
Symfony\Component\VarDumper\Caster\ConstStub {
  +type: "ref"
  +class: "2017-08-30 00:00:00.000000 Europe/Zurich (+02:00)"
  +value: """
    Wednesday, August 30, 2017\n
    +%a from now\n
    DST On
    """
  +cut: 0
  +handle: 0
  +refCount: 0
  +position: 0
  +attr: []
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $cast["\0~\0date"]);
    }

    public function provideDates()
    {
        return array(
            array('2017-04-30 00:00:00.000000', 'Europe/Zurich', '2017-04-30 00:00:00.000000 Europe/Zurich (+02:00)'),
            array('2017-04-30 00:00:00.000000', '+02:00', '2017-04-30 00:00:00.000000 +02:00'),
        );
    }

    /**
     * @dataProvider provideIntervals
     */
    public function testCastInterval($interval_spec, $invert, $expected)
    {
        $interval = new \DateInterval($interval_spec);
        $interval->invert = $invert;

        $xDump = <<<EODUMP
DateInterval {
  interval: "$expected"
  +"invert": $invert
  +"days": false
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $interval);
    }

    public function provideIntervals()
    {
        $ms = function () { return PHP_VERSION_ID >= 70100 ? '.000000' : ''; };

        return array(
            array('PT0S', 0, '0', false),
            array('PT1S', 0, '+ 00:00:01'.$ms(), '.0'),
            array('PT2M', 0, '+ 00:02:00'.$ms(), '.0'),
            array('PT3H', 0, '+ 03:00:00'.$ms(), '.0'),
            array('P4D', 0, '+ 4d'),
            array('P5M', 0, '+ 5m'),
            array('P6Y', 0, '+ 6y'),
            array('P1Y2M3DT4H5M6S', 0, '+ 1y 2m 3d 04:05:06'.$ms()),

            array('PT0S', 1, '0'),
            array('PT1S', 1, '- 00:00:01'.$ms()),
            array('PT2M', 1, '- 00:02:00'.$ms()),
            array('PT3H', 1, '- 03:00:00'.$ms()),
            array('P4D', 1, '- 4d'),
            array('P5M', 1, '- 5m'),
            array('P6Y', 1, '- 6y'),
            array('P1Y2M3DT4H5M6S', 1, '- 1y 2m 3d 04:05:06'.$ms()),
        );
    }
}
