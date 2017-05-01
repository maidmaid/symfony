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
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $interval);
    }

    public function provideIntervals()
    {
        return array(
            array('PT0S', 0, '0'),
            array('PT1S', 0, '+ 00:00:01'),
            array('PT2M', 0, '+ 00:02:00'),
            array('PT3H', 0, '+ 03:00:00'),
            array('P4D', 0, '+ 4 day(s)'),
            array('P5M', 0, '+ 5 month(s)'),
            array('P6Y', 0, '+ 6 year(s)'),
            array('P1Y2M3DT4H5M6S', 0, '+ 1 year(s) 2 month(s) 3 day(s) 04:05:06'),

            array('PT0S', 1, '0'),
            array('PT1S', 1, '- 00:00:01'),
            array('PT2M', 1, '- 00:02:00'),
            array('PT3H', 1, '- 03:00:00'),
            array('P4D', 1, '- 4 day(s)'),
            array('P5M', 1, '- 5 month(s)'),
            array('P6Y', 1, '- 6 year(s)'),
            array('P1Y2M3DT4H5M6S', 1, '- 1 year(s) 2 month(s) 3 day(s) 04:05:06'),
        );
    }

    /**
     * @dataProvider provideTimeZone
     */
    public function testCastTimeZone($timezone, $expected)
    {
        $interval = new \DateTimeZone($timezone);

        $xDump = <<<EODUMP
DateTimeZone {
  timezone: "$expected"
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $interval);
    }

    public function provideTimeZone()
    {
        return array(
            // type 1 (UTC offset)
            array('-12:00', '-12:00'),
            array('+00:00', '+00:00'),
            array('+14:00', '+14:00'),

            // type 2 (timezone abbreviation)
            array('GMT', '+00:00'),
            array('a', '+01:00'),
            array('b', '+02:00'),
            array('z', '+00:00'),

            // type 3 (timezone identifier)
            array('Africa/Tunis', 'Africa/Tunis (+01:00)'),
            array('America/Panama', 'America/Panama (-05:00)'),
            array('Antarctica/Troll', 'Antarctica/Troll (+02:00)'),
            array('Arctic/Longyearbyen', 'Arctic/Longyearbyen (+02:00)'),
            array('Asia/Jerusalem', 'Asia/Jerusalem (+03:00)'),
            array('Atlantic/Canary', 'Atlantic/Canary (+01:00)'),
            array('Australia/Perth', 'Australia/Perth (+08:00)'),
            array('Europe/Zurich', 'Europe/Zurich (+02:00)'),
            array('Indian/Cocos', 'Indian/Cocos (+06:30)'),
            array('Pacific/Tahiti', 'Pacific/Tahiti (-10:00)'),
        );
    }
}
