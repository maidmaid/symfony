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
        $date = (new \DateTime($time))
            ->setTimezone(new \DateTimeZone($timezone))
        ;

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
            array('@1493503200', 'Europe/Zurich', '2017-04-30 00:00:00.000000 +02:00 (Europe/Zurich)'),
            array('2017-04-30 00:00:00.000000', 'Europe/Zurich', '2017-04-30 00:00:00.000000 +02:00 (Europe/Zurich)'),
            array('2017-04-30 00:00:00.000000', '+02:00', '2017-04-30 00:00:00.000000 +02:00')
        );
    }
}
