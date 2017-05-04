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
    public function testCastDate($time, $timezone, $expexted)
    {
        if ((defined('HHVM_VERSION_ID') || PHP_VERSION_ID <= 50509) && preg_match('/[\+|\-]\d{2}:\d{2}/', $timezone)) {
            $this->markTestSkipped('DateTimeZone GMT offsets are supported since 5.5.10. See https://github.com/facebook/hhvm/issues/5875 for HHVM.');
        }

        $date = new \DateTime($time, new \DateTimeZone($timezone));

        $xDump = <<<EODUMP
DateTime @1493503200 {
  date: $expexted
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $date);
    }

    public function provideDates()
    {
        return array(
            array('2017-04-30 00:00:00.000000', 'Europe/Zurich', '2017-04-30 00:00:00.000000 Europe/Zurich (+02:00)'),
            array('2017-04-30 00:00:00.000000', '+02:00', '2017-04-30 00:00:00.000000 +02:00'),
        );
    }
}
