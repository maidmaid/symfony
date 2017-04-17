<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Flag;

class Flag
{
    /**
     * @return FlagInterface
     */
    static public function create($from = false, $prefix = '', $hierarchical = false, $bitfield = 0)
    {
        $onlyInt = true;
        $forceToBinarize = false;

        if (false === $from) {
            $forceToBinarize = true;
        } else {
            foreach (AbstractFlag::search($from, $prefix) as $value) {
                if (!is_int($value)) {
                    $onlyInt = false;
                    break;
                }
            }
        }

        switch (true) {
            case !$forceToBinarize && $onlyInt && !$hierarchical:
                $class = BitFlag::class;
                break;
            case !$forceToBinarize && $onlyInt && $hierarchical:
                $class = HierarchicalFlag::class;
                break;
            default:
                $class = BinarizedFlag::class;
        }

        return new $class($from, $prefix, $bitfield);
    }
}
