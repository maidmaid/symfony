<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Casts DateTimeInterface related classes to array representation.
 *
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class DateCaster
{
    public static function castDate(\DateTimeInterface $d, array $a, Stub $stub, $isNested, $filter)
    {
        $prefix = Caster::PREFIX_VIRTUAL;

        $a = array();
        $a[$prefix.'date'] = new ConstStub(
            $d->format('Y-m-d H:i:s.u'),
            sprintf(
                "literal: %s\ntimestamp: %s\nΔnow: %s",
                $d->format('l, j F Y'),
                $d->getTimestamp(),
                (new \DateTime('now', $d->getTimezone()))->diff($d)->format('%R %yy %mm %dd %H:%I:%S')
            )
        );
        $a[$prefix.'timezone'] = $d->getTimezone();

        return $a;
    }
}
