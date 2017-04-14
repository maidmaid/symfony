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

    public function testCastDate()
    {
        $date = new \DateTime('2017-04-30 00:00:00.000000', new \DateTimeZone('Europe/Zurich'));

        $xDump = <<<'EODUMP'
DateTime {
  date: "2017-04-30 00:00:00.000000"
  timezone: "+02:00 (Europe/Zurich)"
  Δnow: "%s"
  literal: "Sunday, 30 April 2017"
  timestamp: 1493503200
}
EODUMP;

        $this->assertDumpMatchesFormat($xDump, $date);
    }
}
